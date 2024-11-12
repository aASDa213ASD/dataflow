const contextMenu = document.getElementById('context-menu');

function showContextMenu(event)
{
	event.preventDefault();

	if (!event.target.classList.contains('context-menu-interactable'))
	{
		hideContextMenu();
		return;
	}

	contextMenu.style.top = `${event.pageY}px`;
	contextMenu.style.left = `${event.pageX}px`;
	contextMenu.classList.remove('hidden');
}

function hideContextMenu()
{
	contextMenu.classList.add('hidden');
}

document.addEventListener('contextmenu', showContextMenu);
document.addEventListener('click', hideContextMenu);
