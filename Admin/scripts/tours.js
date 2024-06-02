
let add_tour_form = document.getElementById('add_tour_form');
add_tour_form.addEventListener('submit', function(e) {
    e.preventDefault();
    add_tour();
});

function add_tour() {
    let data = new FormData();
    data.append('add_tour', '');
    data.append('name', add_tour_form.elements['name'].value);
    data.append('price', add_tour_form.elements['price'].value);
    data.append('desc', add_tour_form.elements['desc'].value);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/tours.php", true);

    xhr.onload = function() {
        console.log(this.responseText);
        var myModal = document.getElementById('add-tour');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();
        if (this.responseText == 1) {
            alert('success', 'New tour added!');
            add_tour_form.reset();
            get_all_tours();
        } else {
            alert('error', 'Server Down!');
        }
    }

    xhr.send(data);
}


function get_all_tours() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/tours.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('tour-data').innerHTML = this.responseText;
    }
    xhr.send('get_all_tours');
}


let edit_tour_form = document.getElementById('edit_tour_form');

function edit_details(id) {
    edit_tour_form.reset();
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/tours.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        // console.log(this.responseText);
        let data = JSON.parse(this.responseText);
        edit_tour_form.elements['name'].value = data.tourdata.name;
        edit_tour_form.elements['price'].value = data.tourdata.price;
        edit_tour_form.elements['desc'].value = data.tourdata.description;
        edit_tour_form.elements['tour_id'].value = data.tourdata.id;
    }
    xhr.send('get_tour=' + id);
}


edit_tour_form.addEventListener('submit', function(e) {
    e.preventDefault();
    submit_edit_tour();
});


function submit_edit_tour() {
    let data = new FormData();
    data.append('edit_tour', '');
    data.append('tour_id', edit_tour_form.elements['tour_id'].value);
    data.append('name', edit_tour_form.elements['name'].value);
    data.append('price', edit_tour_form.elements['price'].value);
    data.append('desc', edit_tour_form.elements['desc'].value);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/tours.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('edit-tour')
        var modal = bootstrap.Modal.getInstance(myModal)
        modal.hide();
        if (this.responseText == 1) {
            alert('success', 'Tour data edited!');
            edit_tour_form.reset();
            get_all_tours();
        } else {
            alert('error', 'Server Down!');
        }
    }
    xhr.send(data);
}


function toggle_status(id, val) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/tours.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.responseText == 1) {
            alert('success', 'Status toggled!');
            get_all_tours();
        } else {
            alert('error', 'Server Down!');
        }

    }
    xhr.send('toggle_status=' + id + '&value=' + val);
}

let add_image_form = document.getElementById('add_image_form');
add_image_form.addEventListener('submit', function(e) {
    e.preventDefault();
    add_image();
});

function add_image() {
    let data = new FormData();

    data.append('image', add_image_form.elements['image'].files[0]);
    data.append('tour_id', add_image_form.elements['tour_id'].value);
    data.append('add_image','');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/tours.php", true);

    xhr.onload = function() {

        if (this.responseText == 'inv_img') {
            alert('error', 'Only JPG,WEBG or PNG images are allowed!','image-alert');
        } else if (this.responseText == 'inv_size') {
            alert('error', 'Image should be less than 2MB!','image-alert');
        } else if (this.responseText == 'upd_failed') {
            alert('error', 'Image upload failed. Sever Down','image-alert');
        } else {
            alert('success','New Image added!','image-alert');
            tour_images(add_image_form.elements['tour_id'].value,document.querySelector("#tour-images .modal-title").innerText)
            add_image_form.reset();
        }

    }
    xhr.send(data);
}

function tour_images(id,rname)
{
    document.querySelector("#tour-images .modal-title").innerText = rname;
    add_image_form.elements['tour_id'].value = id;
    add_image_form.elements['image'].value = '';

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/tours.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('tour-image-data').innerHTML = this.responseText;

    }
    xhr.send('get_tour_images='+id);
}

function rem_image(img_id,tour_id)
{
    let data = new FormData();

    data.append('image_id',img_id);
    data.append('tour_id', tour_id);
    data.append('rem_image','');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/tours.php", true);

    xhr.onload = function() {

        if (this.responseText == 1) {
            alert('success','Image Removed!','image-alert');
            room_images(tour_id,document.querySelector("#tour-images .modal-title").innerText)
        } 
        else {
            alert('error', 'Image removed failed!','image-alert');
        }

    }
    xhr.send(data);
}


function thumb_image(img_id,tour_id)
{
    let data = new FormData();

    data.append('image_id',img_id);
    data.append('tour_id', tour_id);
    data.append('thumb_image','');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/tours.php", true);

    xhr.onload = function() {

        if (this.responseText == 1) {
            alert('success','Image Thumbnail Changed!','image-alert');
            tour_images(tour_id,document.querySelector("#tour-images .modal-title").innerText)
        } 
        else {
            alert('error', 'Thumbnail update failed!','image-alert');
        }

    }
    xhr.send(data);
}

function remove_tour(tour_id)
{
    if(confirm("Are you sure , you want to delete this tour?"))
    {
        let data = new FormData();

        data.append('tour_id',tour_id);
        data.append('remove_tour','');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/tours.php", true);

        xhr.onload = function() {

            if (this.responseText == 1) {
                alert('success','Tour Removed!');
                get_all_tours();
            } 
            else {
                alert('error', 'Tour removed failed!');
            }

        }
        xhr.send(data);

    }

}
window.onload = function() {
    get_all_tours();
}
