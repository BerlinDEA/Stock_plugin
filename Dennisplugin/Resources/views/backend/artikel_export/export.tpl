{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}

    <div class="panel panel-default">
        <div class="panel-heading"><h2 class="panel-title">Ihre Datei</h2></div>
        <div class="panel-body">
            <a href="{url action='download' file=$file}">Laden Sie Ihre csv-Datei herunter</a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><h2 class="panel-title">Inhalt</h2></div>
        <div class="panel-body">
            <div class="well">{$buffer.log}</div>
        </div>
    </div>

{/block}
