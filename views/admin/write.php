<?php
    $sid = 4;

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT .'/views/core.php';

    $models = ["lib" => "webBuilder"];
    Routing::model(null, $models);

    $wb = new WebBuilder();
?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Write</title>

        <?php
            echo $wb->getStyle('normalize.css');
            echo $wb->getStyle('write.css');
            
            echo $wb->getScript('config.js');
            
        ?>

        <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    </head>
    <body>
        <header>

        </header>
        <main>
            <form action="">
                <section class="write-console">
                    <textarea name="" id="article-title" placeholder="Título" cols="30" rows="10"></textarea>
                    <textarea name="" id="article-description" placeholder="Descripción corta" cols="30" rows="10"></textarea>
                    <div id="editorjs"></div>
                </section>   
                <section class="write-right-bar">
                    <h3>Ajustes del artículo</h3>
                    <div class="write-right-bar-item">
                       <h4>Visibilidad</h4>
                       <select name="" id="">
                            <option value="1">Borrador</option>
                            <option value="1">Publica</option>
                            <option value="1">Oculta</option>
                            <option value="1">Privada</option>
                       </select>
                    </div>

                    <div class="write-right-bar-item">
                        <h4>Fecha de entrega</h4>
                        <div>
                            <input type="checkbox" name="" checked id="article-time-default">
                            <label for="article-time-default">Inmediata</label>
                        </div>
                        <input type="datetime-local" name="" id="">
                    </div>

                    <div class="write-right-bar-item">
                        <h4>Imagen de portada</h4>
                        <?php
                            echo $wb->getImage('icons/cover.jpg', [
                                "class" => "article-image-preview", 
                                "id" => "article-image-preview"
                            ]);
                        ?>
                        <button>Subir Imagen</button>
                        <button>Biblioteca</button>
                    </div>

                    <div class="write-right-bar-item">
                       <h4>Autor</h4>
                       <select name="" id="">
                            <option value="1">Borrador</option>
                            <option value="1">Publica</option>
                            <option value="1">Oculta</option>
                            <option value="1">Privada</option>
                       </select>
                    </div>

                    <div class="write-right-bar-item">
                       <h4>Categoría</h4>
                       <select name="" id="">
                            <option value="1">Borrador</option>
                            <option value="1">Publica</option>
                            <option value="1">Oculta</option>
                            <option value="1">Privada</option>
                       </select>
                    </div>

                    <div class="write-right-bar-item">
                        <h4>Etiquetas</h4>
                        <div id="tags-container">
                            <!-- Aquí se agregarán las nuevas etiquetas -->
                        </div>
                        <input type="text" id="tag-input" placeholder="Buscar etiquetas">
                        <ul id="tag-list" style="display: none;">
                            <!-- Las etiquetas se cargarán dinámicamente a través de JavaScript -->
                        </ul>
                        <button type="button" id="add-tag-btn">Añadir</button>
                    </div>

                       
                </section>       
            </form>
        </main>
    </body>
    <?php
        echo $wb->getScript('write.js');
    ?>
    </html>