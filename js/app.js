$(function(){

    //フッターを最下部に配置
    var $ftr = $('.l-footer');
    if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
        $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight() + 'px')});
    }
    
    //マイページ内の削除ボタンのイベント(ajax)
    $('.js-button-click').on('click', function(){
        var $this = $(this);
        var stampingId = $this.data('stampingid');
        if(stampingId !== undefined && stampingId !== null) {
            $.ajax({
                type: 'post',
                url: 'ajaxDelete.php',
                data: { stampingid : stampingId }
            }).then(function() {
                console.log('ajax Success');
                window.location.reload();
            }).fail(function() {
                console.log('ajax Error');
            });
        }
        return false;
    });

    //セッションメッセージの表示
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if(msg.replace(/(^\s+)|(\s+$)/g, "").length) {
        $jsShowMsg.slideToggle('slow');
        setTimeout(function() {$jsShowMsg.slideToggle('slow');}, 3000);
    }

    //スマホ用ハンバーガーメニューのバーの動き（✕、三）
    $('.js-toggle-sp-menu').on('click', function() {
        $(this).toggleClass('is-active');
    });
   
    //スマホ用header_nvaのスライド（表示・非表示）
    $('.js-toggle-sp-menu').on('click', function () {
       $('.js-header__nav').slideToggle('slow');
    });
  
       
});

