const THROTTLE_DELAY = 150; // milliseconds
let LAST_LOAD_TIME = 0;

async function loadContent(url, object_identifier)
{
	const url_object = new URL(url, window.location.origin);
	const path = url_object.searchParams.get('path');
	const root_window= document.querySelector(object_identifier);

	try
	{
		const response = await fetch(url, {
			headers: { 'X-Requested-With': 'XMLHttpRequest' },
		});

		if (response.ok)
		{
			const content = await response.text();
			await updateBreadcrumbHistory(path);

			// TODO: Don't append scripts to content maybe?
			root_window.innerHTML = content;

			const scripts = root_window.querySelectorAll('script');
			executeScripts(scripts);

			hookHrefs(object_identifier);
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

async function updateBreadcrumbHistory(path)
{
	const header_text = document.querySelector('#header-text');

	if (!path)
	{
		header_text.innerHTML = '';
		return;
	}

	try
	{
		const response = await fetch(`/history?path=${encodeURIComponent(path)}`, {
			headers: { 'X-Requested-With': 'XMLHttpRequest' },
		});

		if (response.ok)
		{
			const breadcrumbHTML = await response.text();
			header_text.innerHTML = breadcrumbHTML;
		}
		else
		{
			console.error('Failed to fetch history:', response.status);
		}
	}
	catch (error)
	{
		console.error('Error updating breadcrumb history:', error);
	}
}

function hookHrefs(object_identifier)
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
			loadContent(url, object_identifier);
		});
	});
}

function executeScripts(scripts)
{
	scripts.forEach((script) =>
	{
		const new_script = document.createElement('script');

		if (script.src)
		{
			new_script.src = script.src;

			new_script.onload = () =>
			{
				console.log(`Loaded script: ${script.src}`)
			}
		}
		else
		{
			new_script.textContent = script.innerHTML;
		}

		document.body.appendChild(new_script);
	});
}