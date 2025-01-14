const context_menu = document.getElementById('context-menu');
const main = document.getElementsByTagName('main')[0];

let file_path = null;

function showContextMenu(event)
{
	event.preventDefault();

	if (!event.target.classList.contains('context-menu-interactable'))
	{
		hideContextMenu();
		return;
	}

	file_path = event.target.getAttribute('data-path');

	context_menu.style.top = `${event.pageY}px`;
	context_menu.style.left = `${event.pageX}px`;
	context_menu.classList.remove('hidden');
}

function hideContextMenu()
{
	context_menu.classList.add('hidden');
}

async function showPropertiesModal()
{
	if (!file_path)
	{
		console.error("No file path available for properties.");
		return;
	}

	const url = new URL('/file/properties/', window.location.origin);
	url.searchParams.append('path', file_path);

	try
	{
		const response = await fetch(url);

		if (!response.ok)
		{
			throw new Error(`HTTP error! Status: ${response.status}`);
		}

		const html = await response.text();
		const modal_window = document.getElementById('modal');
		modal_window.innerHTML = html;
		modal_window.classList.remove('hidden');
	}
	catch (error)
	{
		console.error("Error fetching file properties:", error);
	}
}

function hidePropertiesModal()
{
	const modal_window = document.getElementById('modal');
	modal_window.classList.add('hidden');
	modal_window.innerHTML = '';
}

document.addEventListener('contextmenu', showContextMenu);
document.addEventListener('click', hideContextMenu);
main.addEventListener('scroll', hideContextMenu);