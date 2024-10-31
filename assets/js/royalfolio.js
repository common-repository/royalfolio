jQuery(document).ready(function($) {
"use strict";
    
    // Fire up sortable
    $("#royalfolio-sortable").sortable();
    
    // When sorting has stopped - grab new order and push it to text input
    $("#royalfolio-sortable").sortable({
        stop : function () { 
            var arr = $(this).sortable('toArray');
            var i, n;
            var attrs = [];
            for (i = 0, n = arr.length; i < n; i++) {
                attrs.push($('#' + arr[i]).data('imgurl'));
            }
            $("#royalfolio_image_urls").val(attrs);
        }
    });
    
    // When "Add image(s)" button is clicked
    $("#royalfolio_add_image_button").click(function(e) {
 
        // Prevent default action
        e.preventDefault();
 
        // Frame settings
        var royalfolio_frame = wp.media({
            frame: 'post',
            multiple : true
        });
        
        // When media manager closes
        royalfolio_frame.on("close",function(data) {
            
            // Create empty array to hold image URLs
            var royalfolio_image_urls = [];
            var royalfolio_image_urls_object = {};
            
            // Grab selected images from WordPress media manager
            var royalfolio_selected_images = royalfolio_frame.state().get("selection");

            // Loop selected images and push their URL and sorting to objects
            royalfolio_selected_images.each(function(image) {
                
                royalfolio_image_urls.push(image.attributes.url);
                royalfolio_image_urls_object[image.attributes.id] = image.attributes.url;

            });

            // If the user actually selected any images inside the media manager
            if(royalfolio_image_urls.length !== 0) {
                
                // Append values comma separated in inputs
                $("#royalfolio_image_urls").val(royalfolio_image_urls.join(","));
            
                // Create new array to hold images
                var royalfolio_preview_images = [];

                // Loop images and add some markup around them
                var setThis = $.each(royalfolio_image_urls_object, function(index, value) {

                    royalfolio_preview_images.push('<li id="' + index + '" data-imgurl="' + value + '"><img src="' + value + '" alt=""></li>');

                });

                // Append the previews to the sortable list
                $("#royalfolio-sortable").html(royalfolio_preview_images);
            
            }

        });
 
        royalfolio_frame.open();

    });

});