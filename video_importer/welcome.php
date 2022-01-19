<!-- INCLUDE JQUERY, JAVASCRIPT AND BOOTSTRAP -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- FORM SECTION START -->
<form id="video-search-form" class="form-horizontal" method="post" action="videos.php">
    <div class="text-center">
        <h4> Import Videos From YouTube </h4>
    </div>
    <div class="box-body">

        <!-- Select Search Type Section Start -->
        <div class="form-group">
            <label for="search_type" class="col-sm-3 control-label">
                Select Search Type
            </label>
            <div class="col-sm-10">
            <select class="form-control" name="search_type" onchange="showDiv(this.value)">
                <option value="0">
                    --- Please Select ---
                </option>
                <option value="1">
                    By Channel
                </option>
                <option value="2">
                    By Playlist
                </option>
            </select>
            </div>
        </div>
        <!-- Select Search Type Section End -->

        <!-- Api Key Section Start -->
        <div class="form-group">
            <label for="api-key" class="col-sm-3 control-label">
                API Key
            </label>
            <div class="col-sm-7">
                <input type="text" id="api-key" name="api-key" class="form-control" placeholder="Enter API Key">
            </div>
        </div>
        <!-- Api Key Section End -->

        <!-- Channel Id Section Start -->
        <div class="form-group" id="channel-div">
            <label for="channel-id" class="col-sm-3 control-label">
                Channel Id
            </label>
            <div class="col-sm-7">
                <input type="text" id="channel-id" name="channel-id" class="form-control" placeholder="Enter Channel Id">
            </div>
        </div>
        <!-- Api Key Section End -->

        <!-- User Id Section Start -->
        <div class="form-group" id="user-div">
            <label for="playlist-id" class="col-sm-3 control-label">
                Playlist Id
            </label>
            <div class="col-sm-7">
                <input type="text" id="playlist-id" name="playlist-id" class="form-control" placeholder="Enter Playlist Id">
            </div>
        </div>
        <!-- User Id Section End -->

        <!-- Max Results Section Start -->
        <div class="form-group">
            <label for="max-result" class="col-sm-3 control-label">
                Max Results
            </label>
            <div class="col-sm-7">
                <input type="number" id="max-result" name="max-result" class="form-control" placeholder="Enter Max Results">
            </div>
        </div>
        <!-- Max Results Section End -->

        <!-- Search Button -->
        <div class="col-md-5">
            <button id="search-video" type="submit" class="btn btn-primary">
                Search
            </button>
        </div>
  </div>
</form>

<!-- VIDEOS DISPLAY AREA  -->
<form action="import_videos.php" method="post">  
    <div class="youtube-channel-videos">
    </div>
</form>

<script>
    // Auto Call Function On page load
    showDiv();

    // Set divs on select option
    function showDiv(value) {
        if (value == 1) { 
            document.getElementById("channel-div").style.display = "block";
            document.getElementById("user-div").style.display = "none";
        } else if(value == 2) {
            document.getElementById("user-div").style.display = "block";
            document.getElementById("channel-div").style.display = "none";
        } else {
            document.getElementById("user-div").style.display = "none";
            document.getElementById("channel-div").style.display = "none";
        }
    }

    // VIDEO SEARCH CALL START
    $(document).delegate("#search-video", "click", function(e) {
        e.preventDefault();

        // GET FORM DATA
        var formData = $('#video-search-form').serializeArray();

        // GENERATE AJAX CALL
        var url = "<?='../wp-content/plugins/video_importer/videos.php';?>";
        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (response) { // SUCCESS REPONSE
                // SET VIDEO SECTION AREA
                $('.youtube-channel-videos').html('');

                // CHECK RESPONSE
                if (response.result) {
                    var items = response.result.items;
                    var str = '';
                    var i = 0;

                    // CHECKBOX FOR SELECT ALL VIDEOS
                    str += '<label for="select-all">Select All</label> <input id="select-all" name="select-all" type="checkbox">';
                    
                    // PUSH VIDEOS TO VIDEOS DISPLAY AREA
                    items.map(item => {
                        var id = item.id.videoId;
                        str += '<div class="youtube-channel-video-embed vid-' + id + ' video-' + i++ + '"> <div class="col-3 float-left"> <input type="checkbox" class="video-checkbox" name="video-id[]" value='+ id +'> <iframe style="width:100%" src="https://www.youtube.com/embed/' + id + '" frameborder="0" allowfullscreen>' + item.snippet.title + '</iframe> </div></div>';
                    });

                    str += '<br> <br> <div class="text-center"><button class="btn btn-primary" id="import-videos">IMPORT VIDEOS</button> </div>';

                    if(str) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success...',
                            text: 'Searching Complete!',
                        });

                        // VIDEO DISPLAY AREA
                        $('.youtube-channel-videos').html(str);
                    }

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'No Video Found!',
                    });
                }
            }
        });
    });
    // VIDEO SEARCH CALL END

    // SELECT ALL VIDEOS
    $(document).on( 'click', '#select-all', function () {
        // CHECK ALL VIDEO'S
        if($(this).is(':checked')) {
            $('.video-checkbox').prop('checked', true);
        } else {
            $('.video-checkbox').prop('checked', false);
        }
    }); 

    // IMPORT VIDEO'S CALL START
    $(document).on('click', '#import-videos', function (e) {
        e.preventDefault();

        var api_key = $('#api-key').val();
        var video_ids = [];

        $('.video-checkbox').each(function () {
            // GET SELECTED VIDEO'S ID'S
            if($(this).is(':checked')) {
                video_ids.push($(this).val());
            }
        });

        if (video_ids.length) {
            // GENERATE AJAX CALL
            var url = "<?='../wp-content/plugins/video_importer/import_videos.php';?>";
            $.ajax({
                url: url,
                method: "POST",
                data: {id : video_ids, api_key: api_key},
                dataType: "json",
                success: function (response) { // SUCCESS REPONSE
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success...',
                            text: 'Videos Import Successfully!',
                        });
                    } else if(response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Videos Already Exist!',
                        });
                    }
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'No Video Selected!',
            });
        }
    });
    // IMPORT VIDEOS CALL END
</script>