document.addEventListener('DOMContentLoaded', function () {
	const language_switcher_form = document.getElementById('language-switcher');
	const input = document.createElement('input');
	input.type = 'hidden';
	input.name = aiowps_login_page.slug;

	language_switcher_form.appendChild(input);
});