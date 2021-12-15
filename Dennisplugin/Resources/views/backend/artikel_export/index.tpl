{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}

    <div class="panel panel-default">
        <div class="panel-heading"><h2 class="panel-title">Configuration</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" action="{url controller="Artikel_Export" action="export" __csrf_token=$csrfToken}" method="post">
                <div class="form-group">
                    <label for="lagerselect_form" class="col-sm-2 control-label">Lager Bestand:</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="lagerselect_form" name="lagerselect_form">
                            {foreach $orders as $order}
                                <option value="{$order.supplierID}">{$order.supplierID} - {$order.changetime}</option>
                            {/foreach}
                        </select>

                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" name="submit">Export Vorbereiten</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

{/block}
