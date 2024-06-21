$(document).ready(function() {
let host = document.location;

let GetCitiesUrl = new URL('/get-cities/', host.origin);
$(document).on('change', '#country_id', function (e) {
    e.preventDefault();

    var id =  $(this).val();
    $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
    $.ajax({
               type: 'POST',
               url: GetCitiesUrl + '/' + id + "",
               data:{
                   "state_id": $(this).val() + ""
               },
               success: function (response) {
                   console.log(response);
                   //let html='<option></option>';
                   //for (var i = 0; i < response.data.length; i++) {
                   //    html +=`<option value=\"${response.data[i].id}\">${response.data[i].name}</option>`;
                   //}
                   //$('#city_id').empty();
                   //$('#city_id').append(html);
               }
           });
});
});
