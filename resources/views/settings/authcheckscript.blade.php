  <script>
        var parentWindow = window.parent;
        window.addEventListener("message", (e) => {
            var data = e.data;
            console.log(data);
            if (data.type == 'location') {
                checkForauth(data);
            }
        });

        $(document).ready(function() {
            $('#loadingg').show();
            $('#remove-overlay').show();
          let params = new URLSearchParams(location.search);
          console.log(params);
            let dt = {
                location:params.get('location_id') ||"",
                  token:params.get('sessionkey') ||"",


            }
            if(dt?.token && dt?.location && dt?.token!="" && dt?.location!=""){
                checkForauth(dt);
            }else{
                parentWindow.postMessage('authconnecting', '*');
            }
        });



        let mainusertoken = '';


        function checkForauth(dt) {
            console.log("Checking for URL");
            var url = "{{ route('auth.checking') }}";
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    location: dt.location,
                    token: dt.token
                },
                success: function(data) {
                    console.log(data);
                    toastr.success("Location connected successfully!");
                    mainusertoken = data.token;
                    location.href = "{{ route('dashboard') }}?v="+new Date().getTime();


                },
                error: function(data) {
                    console.log(data);
                },
                complete: function() {
                    $('#loadingg').hide();
                    $('#remove-overlay').hide();
                }
            });
        }
    </script>
