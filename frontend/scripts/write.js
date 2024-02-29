// Arreglo de etiquetas
var tagsArray = ['Nacional', 'Pereira', 'Incendio'];

// Función para cargar las etiquetas en la lista de sugerencias
function loadTags() {
    var tagList = document.getElementById('tag-list');
    tagList.innerHTML = ''; // Limpiar la lista de etiquetas
    tagsArray.forEach(function(tag) {
        var li = document.createElement('li');
        li.textContent = tag;
        li.dataset.tag = tag;
        tagList.appendChild(li);
    });
}

// Cargar las etiquetas iniciales
loadTags();

document.getElementById('add-tag-btn').addEventListener('click', function() {
    var tagInput = document.getElementById('tag-input');
    var tagText = tagInput.value.trim();
    if (tagText !== '' && !tagsArray.includes(tagText)) {
        var tagContainer = document.getElementById('tags-container');
        var tagDiv = document.createElement('div');
        tagDiv.className = 'tag';
        tagDiv.innerHTML = '<span>' + tagText + '</span><button class="remove-btn">×</button>';
        tagContainer.appendChild(tagDiv);
        tagsArray.push(tagText); // Agregar la etiqueta al arreglo
        tagInput.value = ''; // Limpiar el campo de entrada
        loadTags();
    }
});

document.getElementById('tags-container').addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-btn')) {
        var tagToRemove = event.target.parentNode;
        var tagText = tagToRemove.querySelector('span').textContent;
        var index = tagsArray.indexOf(tagText);
        if (index !== -1) {
            tagsArray.splice(index, 1); // Eliminar la etiqueta del arreglo
        }
        tagToRemove.parentNode.removeChild(tagToRemove);
    }
});

document.getElementById('tag-input').addEventListener('input', function() {
    var inputText = this.value.toLowerCase().trim();
    var tagList = document.getElementById('tag-list');
    var tags = tagList.getElementsByTagName('li');
  
    if (inputText === '') {
        tagList.style.display = 'none';
        return;
    }
  
    for (var i = 0; i < tags.length; i++) {
        var tag = tags[i].textContent.toLowerCase();
        if (tag.includes(inputText)) {
            tags[i].style.display = 'block';
        } else {
            tags[i].style.display = 'none';
        }
    }
  
    tagList.style.display = 'block';
    loadTags();
});

document.getElementById('tag-list').addEventListener('click', function(event) {
    if (event.target.tagName === 'LI') {
        var clickedTag = event.target.dataset.tag;
        var tagInput = document.getElementById('tag-input');
        if (!tagsArray.includes(clickedTag)) {
            var tagContainer = document.getElementById('tags-container');
            var tagDiv = document.createElement('div');
            tagDiv.className = 'tag';
            tagDiv.innerHTML = '<span>' + clickedTag + '</span><button class="remove-btn">×</button>';
            tagContainer.appendChild(tagDiv);
            tagsArray.push(clickedTag); // Agregar la etiqueta al arreglo
        }
        tagInput.value = ''; // Limpiar el campo de entrada
        this.style.display = 'none';
    }
});

document.getElementById('tag-input').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        var tagText = this.value.trim();
        if (tagText !== '' && !tagsArray.includes(tagText)) {
            var tagContainer = document.getElementById('tags-container');
            var tagDiv = document.createElement('div');
            tagDiv.className = 'tag';
            tagDiv.innerHTML = '<span>' + tagText + '</span><button class="remove-btn">×</button>';
            tagContainer.appendChild(tagDiv);
            tagsArray.push(tagText); // Agregar la etiqueta al arreglo
            this.value = ''; // Limpiar el campo de entrada
        }
    }
    loadTags();
});


var editor = new EditorJS({
    holder: 'editorjs',
    autofocus: true, // Opciones de configuración adicionales
    tools: {
        header: {
            class: Header,
            inlineToolbar: true
        },
        list: {
            class: List,
            inlineToolbar: true
        },
        image: {
            class: ImageTool,
            config: {
                endpoints: {
                    byFile: 'URL_DE_TU_ENDPOINT_DE_CARGA_DE_IMAGEN',
                    byUrl: 'URL_DE_TU_ENDPOINT_DE_CARGA_DE_IMAGEN'
                }
            }
        },
        // Agrega aquí más herramientas según sea necesario
    }
});
