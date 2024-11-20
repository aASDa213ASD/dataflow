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

async function showDeleteModal() {
    if (!file_path) {
        console.error("No file path available for deletion.");
        return;
    }

    const url = new URL('/file/delete/', window.location.origin); // Adjust to your endpoint
    url.searchParams.append('path', file_path);

    try 
	{
        // Fetch and display the delete confirmation modal
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        // Display the modal with content from delete.html.twig
        const html = await response.text();
        const modal_window = document.getElementById('modal');
        modal_window.innerHTML = html;
        modal_window.classList.remove('hidden');

        const confirmDelete = modal_window.querySelector('#confirmDelete');
        if (confirmDelete) {
            confirmDelete.addEventListener('click', async (event) => {
                event.preventDefault();

                try 
				{
                    const delete_url = new URL('/file/delete', window.location.origin);
					delete_url.searchParams.append('path', file_path);
                    const postResponse = await fetch(delete_url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ path: file_path }),
                    });

                    if (!postResponse.ok) 
					{
                        throw new Error(`HTTP error! Status: ${postResponse.status}`);
                    }

                    modal_window.classList.add('hidden');
                    window.location.reload();
                } 
				catch (postError) 
				{
                    console.error("Error submitting the deletion request:", postError);
                }
            });
        }
		handleConfirmDelete();
    } 
	catch (error) 
	{
        console.error("Error deleting file:", error);
    }
}

async function handleConfirmDelete() {
    const confirmDeleteButton = document.getElementById('confirmDelete');

    if (!confirmDeleteButton) {
        console.error("Confirm delete button not found. This method will not execute.");
        return;
    }

    confirmDeleteButton.addEventListener('click', async (event) => {
        event.preventDefault();

        if (!file_path) {
            console.error("File path is not provided for deletion.");
            alert("Error: File path is missing. Unable to proceed with deletion.");
            return;
        }

        // Extract the current path from the query string
        const urlParams = new URLSearchParams(window.location.search);
        const currentPath = urlParams.get('path'); // Get the `path` parameter

        if (!currentPath) {
            console.error("Current path is not available in the URL.");
            alert("Error: Unable to determine the current directory. Please ensure the URL contains a valid path.");
            return;
        }

        console.log("Current path extracted:", currentPath); // Debugging log

        try {
            const deleteUrl = new URL('/file/delete', window.location.origin);

            // Send the POST request to the server
            const response = await fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ path: file_path }),
            });

            if (response.ok) {
                // Remove the file from the UI
                const fileElement = document.querySelector(`[data-path="${file_path}"]`);
                if (fileElement) {
                    fileElement.remove(); // Remove the deleted file from the UI
                }

                alert(`File deleted successfully.`);
            } else {
                const errorMessage = await response.text();
                console.error("Failed to delete the file:", errorMessage);
                alert(`Error: Could not delete the file. Server responded with status ${response.status}.`);
            }
        } catch (error) {
            console.error("Error handling file deletion:", error);
            alert("Error: Something went wrong while deleting the file.");
        }
    });
}




function hideDeleteModal()
{
	const modal_window = document.getElementById('modal');
	modal_window.classList.add('hidden');
	modal_window.innerHTML = '';
}

document.addEventListener('contextmenu', showContextMenu);
document.addEventListener('click', hideContextMenu);
main.addEventListener('scroll', hideContextMenu);