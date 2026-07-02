(function ($, window, document, undefined) {

	ResponsiveBackground = (function () {

		function Constructor() {
			let resizeTimeout;

			// Method to load the background
			function loadBackground(section) {
				const isMobile = window.innerWidth <= 800;
				const desktopImage = section.dataset.desktop || "";
				const mobileImage = section.dataset.mobile || desktopImage; // Fallback to desktop if mobile is not provided

				const backgroundImage = isMobile ? mobileImage : desktopImage;

				if (section.style.getPropertyValue("--background-image") !== `url('${backgroundImage}')`) {
					section.style.setProperty("--background-image", `url('${backgroundImage}')`);
				}
			}

			// Observer setup
			function observeSections(sections) {
				const observer = new IntersectionObserver(
					(entries) => {
						entries.forEach((entry) => {
							if (entry.isIntersecting) {
								loadBackground(entry.target);
							}
						});
					},
					{ rootMargin: "0px", threshold: 0.1 }
				);

				sections.forEach((section) => observer.observe(section));
			}

			// Immediate resize handling
			function immediateHandleResize(sections) {
				sections.forEach((section) => {
					loadBackground(section);
				});
			}

			// Debounce resize handling
			function debounceResize(sections) {
				clearTimeout(resizeTimeout);
				resizeTimeout = setTimeout(() => immediateHandleResize(sections), 200);
			}

			// Start method for initialization
			this.start = function () {
				const sections = document.querySelectorAll(".responsive-background");

				if (sections.length > 0) {
					observeSections(sections);
					window.addEventListener("resize", () => debounceResize(sections));
					immediateHandleResize(sections);
				}
			};
		}

		return new Constructor();

	})();

})(jQuery, window, document);
