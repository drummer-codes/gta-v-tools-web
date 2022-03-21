<div class="modal fade" id="ingame_guide" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">In-Game Tools: Usage Guide</h5>
            </div>
            <div class="modal-body">
                <h6>Step 1</h6>
                <p>
                    Download and install <a href="<?php echo $_WSS; ?>/gta/static/ingame/BadMusicianGTATools_1.1.zip" class="text-bm" target="_blank">BadMusicianGTATools_1.1.zip</a> RagePluginHook plugin.
                </p>
                <h6>Step 2</h6>
                <p>
                    Load the plugin using console command <code>LoadPlugin "BadMusicianGTATools.dll"</code>
                </p>
                <h6>Step 3</h6>
                <p>
                    Copy your token by clicking <a href="#" class="btn btn-sm btn-success" id="ingame_token_copy_doc"><i class="fas fa-copy"></i></a> in the <b>In-Game Tools</b> window
                </p>
                <h6>Step 4</h6>
                <p>
                    Connect the plugin using console command <code>BadMusicianGTATools_Connect</code> while having the token copied to your clipboard
                </p>
                <h6>Step 5</h6>
                <p>
                    Use the <a href="#" class="btn btn-sm btn-success"><i class="fas fa-play-circle"></i> In-Game</a> buttons to send tasks to the game. Use the <a href="#" class="btn btn-sm btn-info"><i class="fas fa-exclamation-triangle me-2"></i> Stop all tasks</a> to clear the tasks.
                </p>
                <h6 class="text-bm">After you are done</h6>
                <p>
                    Disconnect the plugin using console command <code>BadMusicianGTATools_Disconnect</code>, then unload the plugin using console command <code>UnloadPlugin "BadMusicianGTATools.dll"</code>
                </p>
                <h6 class="text-orange">What if I lost my token?</h6>
                <p>
                    Obtain a new token by clicking <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-sync-alt"></i></a> in the <b>In-Game Tools</b> window. Don't forget to <b>reconnect</b> the plugin as described in Steps 3-4
                </p>
                <div class="alert alert-warning mt-3" role="alert">
                    In-Game Tools are currently available for <a class="text-light font-weight-bold" href="/gta/animations/">Animations</a>, <a class="text-light font-weight-bold" href="/gta/sounds/">Sounds</a>, and <a class="text-light font-weight-bold" href="/gta/speech/">Speeches</a> libraries
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php static_html('bm_copyright'); ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous"></script>
<script src="//cdn.jsdelivr.net/npm/js-cookie@rc"></script>
<script src="/gta/static/js/common.js?<?php echo time(); ?>"></script>
<?php foreach ($_SCRIPTS as $x) echo '<script src="' . $x . '?' . time() . '"></script>'; ?>
</body>

</html>