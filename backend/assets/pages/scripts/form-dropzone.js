var FormDropzone = function () {


    return {
        //main function to initiate the module
        init: function () {

            Dropzone.options.myDropzone = {
                dictDefaultMessage: "",
                maxFilesize: 25, // MB
                acceptedMimeTypes: ".png, .jpg, .pdf, .git, .tiff, .tif, .txt, .doc, .docx, .xls, .xlsx, .csv, .jpeg",
                init: function () {
                    this.on("addedfile", function (file) {
                        if (this.files.length) {
                            var _i, _len;
                            for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) {
//                                alert(this.files[_i].name +"===" +file.name +"==="+this.files[_i].size +"==="+ file.size);
                                if (this.files[_i].name === file.name && this.files[_i].size == file.size) {
                                    alert("There is not allowed to upload duplicate file");
                                    this.removeFile(file);
                                }
                            }
                            var _j, _len1;
                            var total_files = $("input[name='upload_filename[]']").map(function () {
                                return $(this).val();
                            }).get();
                            var total_filesize = $("input[name='upload_filesize[]']").map(function () {
                                return $(this).val();
                            }).get();
                            var new_file = this;


                            $(total_files).each(function (i, item) {
                                var value = total_files[i];
                                var size = total_filesize[i];
                                if ($.trim(value) == $.trim(file.name) && size == file.size) {
                                    alert("There is not allowed to upload duplicate file");
                                    new_file.removeFile(file);
                                }
                            });

                        }

                        // Create the remove button
                        var removeButton = Dropzone.createElement("<a href='javascript:;'' data-id='' class='btn btn-danger btn-sm btn-block' onclick ='remove_file(this);'>Remove</a>");

                        // Capture the Dropzone instance as closure.
                        var _this = this;

                        // Listen to the click event
                        removeButton.addEventListener("click", function (e) {

                            // Make sure the button click doesn't submit the form:
                            e.preventDefault();
                            e.stopPropagation();

                            // Remove the file preview.
                            _this.removeFile(file);
                            // If you want to the delete the file on the server as well,
                            // you can do the AJAX request here.
                        });

                        // Add the button to the file preview element.
                        file.previewElement.appendChild(removeButton);
                    }),
                            this.on("success", function (file, data) {
                                
                                File_Data = JSON.parse(data);
                                var filename = File_Data.name;
                                var file_size = File_Data.size;
                                
                               
                                
                                var filedetail = Dropzone.createElement("<input type=hidden name=attachment[] value=" + filename + ">");
                                var filesize = Dropzone.createElement("<input type=hidden name=size[] value=" + file_size + ">");
                                file.previewElement.appendChild(filedetail);
                                file.previewElement.appendChild(filesize);

                                var ext = checkFileExt(file.name); // Get extension
                                var newimage = "";
                                // Check extension
                                if (ext != 'png' && ext != 'jpg' && ext != 'jpeg') {
                                    newimage = site_url + "assets/layouts/layout/img/file_icons/" + ext + ".png"; // default image path
                                }

                                this.createThumbnailFromUrl(file, newimage);


                            });
                }
            }
        }
    };
}();

jQuery(document).ready(function () {
    FormDropzone.init();

});
// Get file extension
function checkFileExt(filename) {
    filename = filename.toLowerCase();
    return filename.split('.').pop();
}