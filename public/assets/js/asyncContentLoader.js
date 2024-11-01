const THROTTLE_DELAY = 100; // milliseconds
let LAST_LOAD_TIME = 0;

async function loadContent(url) {
	const root_window = document.querySelector('#root-window');

	try
	{
		const response = await fetch(url, {
			headers: { 'X-Requested-With': 'XMLHttpRequest' },
		});

		if (response.ok)
		{
			const content = await response.text();
			root_window.innerHTML = content;
			hookHrefs();
		}
		else
		{
			console.error('Failed to fetch content:', response.status);
		}
	}
	catch (error)
	{
		console.error('Error loading content:', error);
	}
}

function hookHrefs()
{
	document.querySelectorAll('a[async]').forEach(link =>
	{
		link.addEventListener('click', (event) =>
		{
			const currentTime = Date.now();

			if (currentTime - LAST_LOAD_TIME < THROTTLE_DELAY)
			{
				event.preventDefault();
				return;
			}

			LAST_LOAD_TIME = currentTime;
			event.preventDefault();
			const url = link.getAttribute('href');
			loadContent(url);
		});
	});
}