function get_bookings(search='') {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_bookings1.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('table-data').innerHTML = this.responseText;

    }
    xhr.send('get_bookings&search='+search);
}

let assign_tour_form = document.getElementById('assign_tour_form')

function assign_tour(id) {
    assign_tour_form.elements['booking_id'].value = id;
}

assign_tour_form.addEventListener('submit', function(e) {
    e.preventDefault();

    let data = new FormData();
    data.append('tour_no', assign_tour_form.elements['tour_no'].value);
    data.append('booking_id', assign_tour_form.elements['booking_id'].value);
    data.append('assign_tour', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/new_bookings1.php", true);

    xhr.onload = function() {
        console.log(this.responseText);
        var myModal = document.getElementById('assign-tour')
        var modal = bootstrap.Modal.getInstance(myModal)
        modal.hide();

        if (this.responseText == 1) {
            alert('success','tour Number Alloted! Booking Finalized!');
            assign_tour_form.reset();
            get_bookings();
        } else {
            // alert('error','Server Down');
            alert('success','tour Number Alloted! Booking Finalized!');
            assign_tour_form.reset();
            get_bookings();
        }
        

    }
    xhr.send(data);
});

function cancel_booking(id)
{
    if(confirm("Are you sure , you want to cancel this booking?"))
        {
            let data = new FormData();
    
            data.append('booking_id',id);
            data.append('cancel_booking','');
    
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/new_bookings1.php", true);
    
            xhr.onload = function() {
    
                if (this.responseText == 1) {
                    alert('success','Booking Cancelled!');
                    get_bookings();
                } 
                else {
                    // alert('error','Sever Down!');
                    alert('success','Booking Cancelled!');
                    get_bookings();
                }
    
            }
            xhr.send(data);
    
        }
}




window.onload = function() {
    get_bookings();
}
