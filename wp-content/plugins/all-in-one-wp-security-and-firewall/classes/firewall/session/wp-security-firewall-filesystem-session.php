<?php
namespace AIOWPS\Firewall;
// phpcs:ignoreFile WordPress.WP.AlternativeFunctions -- We cannot guarantee that the methods used in this class will be run within a WordPress context. As a consequence, we are unable to use the suggested WordPress functions.

defined('AIOWPS_FIREWALL_DIR') || die('No direct access allowed.');

/**
 * Implementation of the firewall's sessions using the filesystem as the storage mechanism
 */
class Filesystem_Session extends Session_Storage {

	/**
	 * Gives each session file our file prefix
	 */
	use File_Prefix_Trait;

	/**
	 * Stores the directory where the sessions will be stored
	 *
	 * @var string
	 */
	protected $workspace;

	/**
	 * The full path to the particular session file
	 *
	 * @var string
	 */
	protected $full_path;

	/**
	 * Subdirectory where the particular session file is stored
	 *
	 * @var string
	 */
	protected $subdir;

	/**
	 * Period (in days) to prune the sessions
	 *
	 * @var int
	 */
	protected $prune_period;

	/**
	 * The default amount of days before pruning session files
	 */
	const DEFAULT_PRUNE_DAYS = 5;

	/**
	 * Tells the update method not to write the contents
	 */
	const DO_NOT_WRITE = -1;

	/**
	 * Clears the value of the given key
	 *
	 * @param string $key
	 * @return void
	 */
	public function clear($key) {
		$this->update(function($contents) use ($key) {

			if (!isset($contents[$key])) return self::DO_NOT_WRITE;

			unset($contents[$key]);

			return $contents;
		});
	}

	/**
	 * Prunes all session files that are X amount of days old
	 *
	 * @return void
	 */
	public function prune() {
		foreach ($this->session_files() as $file) {
			$last_modified = floor((time() - $file->getMTime()) / 86400);
			if ($last_modified >= $this->prune_period) {
				$this->attempt_remove_session($file->getRealPath());
			}
		}
	}

	/**
	 * Purge all session files in the session directory
	 *
	 * @return void
	 */
	public function purge() {
		foreach ($this->session_files() as $file) {
			$this->attempt_remove_session($file->getRealPath());
		}
	}

	/**
	 * Attempts to remove the session file and its parent directories
	 *
	 * @param string $path
	 * @return void
	 */
	protected function attempt_remove_session($path) {

		if (!unlink($path)) return;

		$parent = dirname($path);
		$parentparent = dirname($parent);

		// Suppress errors: parent directories may be non-empty, already removed, or modified concurrently,
		// so the cleanup is best-effort only and we want to suppress any warnings that may arise due to this.
		// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- rationale above
		if (@rmdir($parent)) @rmdir($parentparent);
	}

	/**
	 * Deletes all the session files, their parent directories, and deletes the session directory
	 *
	 * @return bool
	 */
	public function destroy() {
		$iterator = new \RecursiveDirectoryIterator($this->workspace, \FilesystemIterator::SKIP_DOTS);
		$files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

		foreach ($files as $file) {
			if ($file->isDir()) {
		   		rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}

		return rmdir($this->workspace);
	}

	/**
	 * Returns the workspace
	 *
	 * @return string
	 */
	public function get_workspace() {
		return $this->workspace;
	}

	/**
	 * Iterates the session files
	 *
	 * @param array $ignore_files
	 * @return iterable
	 */
	protected function session_files($ignore_files = array()) {

		if (!file_exists($this->workspace) || !is_dir($this->workspace)) return;
		
		$ignore_files[] = Utility::normalize_path($this->workspace.'/index.php');
		
		$itr = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->workspace, \FilesystemIterator::SKIP_DOTS));

		foreach ($itr as $file) {
			$path = Utility::normalize_path($file->getRealPath());

			if (false === $path || in_array($path, $ignore_files)) continue;

			yield $file;
		}
	}

	/**
	 * Atomically increment the counter stored at $key
	 *
	 * @param string $key
	 * @return void
	 */
	public function increment($key) {
		$this->update(function($contents) use ($key) {

			if (!isset($contents[$key])) $contents[$key] = 0;

			++$contents[$key];

			return $contents;

		});
	}

	/**
	 * Safely read the session data at the index of $key
	 * This should only be used to read the session data only; do not use the data returned to modify and write the session data.
	 *
	 * @param string $key
	 * @return mixed|bool - Returns the data on success; false on failure
	 */
	public function read($key) {
		return $this->process_file(function($contents) use ($key) {
			return isset($contents[$key]) ? $contents[$key] : false;
		});
	}

	/**
	 * Atomically update the session contents.
	 *
	 * @param callable $cb - The callback where you can atomically modify the data
	 * @return mixed|bool
	 */
	public function update(callable $cb) {
		return $this->process_file($cb, true);
	}

	/**
	 * Atomically process the session file
	 *
	 * @param callable $cb   - The callback to apply to the contents of the session file
	 * @param boolean $write - Whether to write the contents back to the session file
	 * @return mixed
	 */
	protected function process_file(callable $cb, $write = false) {

		clearstatcache();
		if (!file_exists($this->full_path) && !$this->init_session_file()) return false;

		$locked = false;
		$fp = null;

		try {

			if (!is_readable($this->full_path)) {
				throw new \Exception("Unable to process the session file. Check you have the required read permissions.");
			}
			
			if ($write) {
				if (!is_writeable($this->full_path)) {
					throw new \Exception("Unable to process the session file. Check you have the required write permissions.");
				}
			}

			$fp = fopen($this->full_path, ($write ? 'r+' : 'r'));
			if (false === $fp) {
				throw new \Exception("Unable to acquire a file handle for the session data.");
			}

			if (false === flock($fp, ($write ? LOCK_EX : LOCK_SH))) {
				throw new \Exception("Unable to acquire a lock for the session data.");
			}
		
			$locked = true;

			$contents = stream_get_contents($fp, -1, strlen(self::get_file_content_prefix()));
			if (false === $contents) {
				throw new \Exception("Unable to read the session data.");
			}

			$contents = json_decode($contents, true);
			if (!is_array($contents)) $contents = array();

			$contents = $cb($contents);

			if ($write && self::DO_NOT_WRITE !== $contents) {
				ftruncate($fp, 0);
				rewind($fp);

				$encoded_contents = json_encode($contents);
				if (false === $encoded_contents) {
					throw new \Exception("Unable to encode the session data.");
				}

				if (false === fwrite($fp, self::get_file_content_prefix() . $encoded_contents)) {
					throw new \Exception("Unable to write the session data.");
				}

				fflush($fp);
			}

			return $contents;

		} catch (\Exception $e) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- PCP warning. Part of AIOS error reporting system.
			error_log("AIOS sessions: " . $e->getMessage());
			return false;
		} finally {
			if ($locked) flock($fp, LOCK_UN);
			if (is_resource($fp)) fclose($fp);
		}
	}

	/**
	 * Initialise the filesystem implementation
	 *
	 * @param array $config
	 * @return void
	 */
	protected function init(array $config) {
		
		$this->workspace    = isset($config['workspace']) ? Utility::untrailingslashit($config['workspace']) : '';
		$this->prune_period = empty($config['prune_period']) ? self::DEFAULT_PRUNE_DAYS : $config['prune_period'];
		$session_name = $this->get_session_name();
		$this->subdir = Utility::normalize_path(chunk_split(substr($session_name, 0, 4), 2, '/'));

		clearstatcache();
		if (!file_exists($this->workspace)) Utility::wp_mkdir_p($this->workspace);

		// Create an index file at the workspace to help prevent unwanted browsing
		Utility::create_index_file($this->workspace);

		$this->full_path = $this->workspace.'/'.$this->subdir.$session_name.".php";
	}

	/**
	 * Initialises the session file
	 *
	 * @return bool
	 */
	protected function init_session_file() {
		if (!file_exists($this->workspace.'/'.$this->subdir)) Utility::wp_mkdir_p(($this->workspace.'/'.$this->subdir));
		return (false !== file_put_contents($this->full_path, self::get_file_content_prefix() . json_encode(array()), LOCK_EX));
	}

	/**
	 * Get the session name to use for the filesystem
	 *
	 * @return string
	 */
	protected function get_session_name() {
		return hash('sha256', parent::get_session_name() . Session_Utility::get_salt());
	}

	/**
	 * Overwrite the prefix description from File_Prefix_Trait
	 *
	 * @return string
	 */
	public static function get_prefix_description() {
		$desc  = " * The file is required for storing and retrieving the firewall's session data about this client.\n";
		$desc .= " * You may change the session path by setting the AIOS_SESSION_PATH constant in wp-config.\n";
		return $desc;
	}

}
