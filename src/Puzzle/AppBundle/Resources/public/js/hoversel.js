jQuery(document).ready(function ($) {
    var metierTitle1 = $('#title1'),
            autoPlayId,
            autoPlayDelay = 5000;
    var metierTitle2 = $('#title2'),
            autoPlayId,
            autoPlayDelay = 5000;
	var metierTitle3 = $('#title3'),
            autoPlayId,
            autoPlayDelay = 5000;
    var metierContent1 = $('#content1'),
            autoPlayId,
            autoPlayDelay = 5000;
    var metierContent2 = $('#content2'),
            autoPlayId,
            autoPlayDelay = 5000;
	var metierContent3 = $('#content3'),
            autoPlayId,
            autoPlayDelay = 5000;
    metierTitle1.hover(function () {
        if (metierTitle1.hasClass('active')) {}
        else {
            metierTitle1.addClass('active');
            metierTitle1.removeClass('title');
            metierTitle2.addClass('title');
            metierTitle2.removeClass('active');
            metierTitle3.addClass('title');
            metierTitle3.removeClass('active');
            metierContent1.addClass('active');
            metierContent2.removeClass('active');
            metierContent3.removeClass('active');
        }
    });
    metierTitle2.hover(function () {
        if (metierTitle2.hasClass('active')) {}
        else {
            metierTitle1.addClass('title');
            metierTitle1.removeClass('active');
            metierTitle2.addClass('active');
            metierTitle2.removeClass('title');
            metierTitle3.addClass('title');
            metierTitle3.removeClass('active');
            metierContent1.removeClass('active');
            metierContent2.addClass('active');
            metierContent3.removeClass('active');
        }
    });
    metierTitle3.hover(function () {
        if (metierTitle3.hasClass('active')) {}
        else {
            metierTitle1.addClass('title');
            metierTitle1.removeClass('active');
            metierTitle2.addClass('title');
            metierTitle2.removeClass('active');
            metierTitle3.addClass('active');
            metierTitle3.removeClass('title');
            metierContent1.removeClass('active');
            metierContent2.removeClass('active');
            metierContent3.addClass('active');
        }
    });


/********************************** Album round *******************************/
  var started;
  var album = $('#album-random div').length;
  function randAlbum(){
    round1 = Math.floor(Math.random() * album + 1);
    round2 = Math.floor(Math.random() * album + 1);
    //$( "#album-random div:nth-child(" + round1 + ")").append( $("#album-random div:nth-child(" + round2 + ")") );
    $('#album-random').prepend($('#album-random').children().eq(album - 1));
    console.log(round1);
    console.log(round2);
  }
  setInterval(randAlbum, 3000);

});
