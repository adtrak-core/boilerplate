import adminNotice from "./AdminNotice";

import * as $ from 'jquery';


let MediaUploader = function(selector, fieldname) {
    fieldname = fieldname || "mbp_form_fields";
    let selectButton;
    let mediaUploaderContainer;
    let loadedItems = [];

    const instance = this;

    let locale = {
        add_photos: "Add photo or video",
        invalid_media_type: "Invalid media type selected, only images and videos are supported",
        one_image: "Currently the Google My Business API only supports uploading a single image or video to a post",
        image_too_small: "Post image must be at least 250x250px.",
        image_file_too_small: "Post image must be at least 10KB",
        image_file_too_big: "Post image must be at most 5MB",
    };

    let wp_media_uploader = wp.media({
        frame:    "post",
        state:    "insert",
        multiple: false
    });

   this.setFieldName = function(name){
        fieldname = name;
    };

    const clickSelectButton = function(){
        wp_media_uploader.open();
    };

    const drawContainer = function(){
        mediaUploaderContainer = $("<div>", {"class": "media-uploader-container"});
        selector.append(mediaUploaderContainer);
    };

    const drawSelectButton = function(){
        selectButton = $("<div>", {"class": "item new-item-button"});
        selectButton.html(
            "<div class='new-item-text'>" +
                "<span class=\"dashicons dashicons-camera\"></span><br />"+ locale.add_photos +
            "</div>"
        );
        selectButton.click(clickSelectButton);
        mediaUploaderContainer.append(selectButton);
    };

    const clickDeleteButton = function(event){
        event.preventDefault();
        this.closest('div').remove();
    };

    this.countItems = function(){
        return $(".item.item-container").length;
    };

    this.clearItems = function(){
        $(".item.item-container").remove();
    };

    this.loadItem = function(type, itemUrl, thumbnail){
        if(instance.countItems() >= 1){
            adminNotice(locale.one_image);
            return;
        }
        let newItem = $("<div>", {"class": "item item-container"});


        if(type === "VIDEO" || !thumbnail){
            let externalImage = $("<div>", { "class": "external-image" });
            newItem.append(externalImage);
        }else{
            let newItemImg = $("<img>", {"class": "item-image", "src": thumbnail});
            newItem.append(newItemImg);
        }

        let newItemInput = $("<input>", {"type": "hidden", "value": itemUrl, "name": fieldname + "[mbp_post_attachment]"});
        let newItemType = $("<input>", {"type": "hidden", "value": type, "name": fieldname + "[mbp_attachment_type]"});
        let deleteButton = $("<button>");
        deleteButton.html("<span class=\"dashicons dashicons-trash\"></span>");
        newItem.append(deleteButton);

        newItem.append(newItemInput);
        newItem.append(newItemType);

        deleteButton.click(clickDeleteButton);
        mediaUploaderContainer.append(newItem);
    };

    this.loadItems = function(items){

    };

    /* Media was submitted through the "Add from URL" dialog */
    wp_media_uploader.on("select", function(){
        let json = wp_media_uploader.state().props.toJSON();
        if(json.width){
            instance.loadItem("PHOTO", json.url, json.url);
        }else{
            instance.loadItem("VIDEO", json.url);
        }

    });

    /* Media was selected from the media library */
    wp_media_uploader.on("insert", function(){
        let json = wp_media_uploader.state().get("selection").toJSON();
        //console.log(json);
        //json.forEach(element => loadItem(element.type, element.url, element.sizes.medium.url));
        json.forEach((element) => {
            if(element.type === "image"){
                if(element.width < 250 || element.height < 250){
                    adminNotice(locale.image_too_small);
                    return;
                }else if(element.filesizeInBytes < 10240){ //10KB
                    adminNotice(locale.image_file_too_small);
                    return;
                }else if(element.filesizeInBytes > 5242880){ //5MB
                    adminNotice(locale.image_file_too_big);
                    return;
                }

                //Use the thumbnail size if the image is too small to have a medium size image
                instance.loadItem("PHOTO", element.url, typeof element.sizes.medium !== 'undefined' ? element.sizes.medium.url : element.sizes.thumbnail.url);
            }else if(element.type === "video"){
                instance.loadItem("VIDEO", element.url, element.thumb.src);
            }else{
                adminNotice(locale.invalid_media_type);
            }
        });
        //

        // let image_url = json.url;
        //
        // $('#meta-image').val(image_url);
        // $('#meta-image-preview').attr('src',image_url);
        // $('input[name="mbp_attachment_type"]').val('PHOTO');
    });



    drawContainer();
    drawSelectButton();
};


export default MediaUploader;
