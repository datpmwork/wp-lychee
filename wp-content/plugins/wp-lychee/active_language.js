jQuery(document).ready(
    function(){
        var lastMode = "";
        qTranslateConfig.qtx.addLanguageSwitchBeforeListener(
            function(){
                if (window.kc) {
                    lastMode = kc.cfg.mode;
                    if (kc.cfg.mode == 'kc') {
                        window.kc.switch();
                    }
                }
            }
        );
        qTranslateConfig.qtx.addLanguageSwitchAfterListener(
            function(){
                if (window.kc) {
                    if (lastMode == 'kc') {
                        window.kc.switch();
                    }
                }
            }
        );
    }
);