/**
 * Created by zacharyrosenthal on 12/11/15.
 */
/**
 * Called when product key is entered
 * will show unscrambled media if correct key
 */
function unlock() {

    //grab form values
    var key = $('#key-input').val();
    var type = $('#media-type').val()

    //get file extension
    var item = type.split(".")[1];

    //call unlock on server
    $.ajax({
        url: "/" + type,
        data: {key: key},
        type: "POST",
        success: function (data) {

            //show the actual media object
            showMedia(item, 'u');
        }
    });
}

/**
 * called when media is "purchased"
 * @param item - file extension
 */
function purchase(item) {

    //call purchase on the server
    $.ajax({
        url: "/test." + item,
        type: "GET",
        success: function (data) {
            data = JSON.parse(data);

            //display the product key
            $("#product-key").attr('value', data).show();

            //show content area
            $("#main-content").show();

            //show actual media
            showMedia(item, 's');

        }
    });
}

/**
 * callled from other js functions
 *
 * will add appropriate element to page
 *
 * @param item
 * @param id
 */
function showMedia(item, id) {

    var elm = $('#' + id);
    
    elm.empty();

    if (item == "mp3") {

        elm.prepend("<video src='/show/" + id + "/test.mp3' controls></video>")


    } else if (item == "mp4") {

        elm.prepend("<video height='300' width='auto' src='/show/" + id + "/test.mp4' controls></video>")

    } else if (item == "jpg") {

        elm.prepend("<img height='300' width='auto' src='/show/" + id + "/test.jpg'>")

    }

}