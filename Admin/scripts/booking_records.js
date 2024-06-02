function get_bookings(search='', page=1) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/booking_records.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        try {
            let data = JSON.parse(this.responseText);
            console.log(data);  // Kiểm tra nội dung phản hồi JSON

            document.getElementById('table-data').innerHTML = data.table_data;
            document.getElementById('table-pagination').innerHTML = data.pagination;
        } catch (e) {
            console.error("Parsing error:", e);
            console.log("Response Text:", this.responseText);  // In ra nội dung phản hồi khi có lỗi phân tích cú pháp JSON
        }
    };
    
    xhr.send('get_bookings&search=' + search + '&page=' + page);
}

function change_page(page) {
    get_bookings(document.getElementById('search_input').value, page);
}

function download(id) {
    window.location.href = 'generate_pdf.php?gen_pdf&id=' + id;
}

window.onload = function() {
    get_bookings();
}
