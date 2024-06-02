<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <?php require('Inc/link.php') ?>
    <title><?php echo $settings_r['site_title'] ?> - Tour</title>
</head>

<body class="bg-light">
    <?php require('Inc/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Our Tour -->
            <div class="my-5 px-4">
                <h2 class="fw-bold h-font text-center">TOUR</h2>
                <div class="h-line bg-dark"></div>
            </div>

            <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 ps-4">
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
                    <div class="container-fluid flex-lg-column align-items-stretch">
                        <h4 class="mt-2"></h4>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filterDropdown">
                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                                    <span>ENTER NAME</span>
                                    <button id="name_search_btn" onclick="name_search_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                                </h5>
                                <input type="text" id="search_name" class="form-control shadow-none mb-3" placeholder="Nhập tên tour" oninput="name_search_filter()">
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="col-lg-8 col-md-12 px-4" id="tours-data">
                <!-- Tours data will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        let tours_data = document.getElementById('tours-data');
        let search_name = document.getElementById('search_name');
        let name_search_btn = document.getElementById('name_search_btn');

        function fetch_tours() {
            let name_search = search_name.value;

            let xhr = new XMLHttpRequest();
            xhr.open("GET", "ajax/tours.php?fetch_tours&name_search=" + encodeURIComponent(name_search), true);
            xhr.onprogress = function() {
                tours_data.innerHTML = `<div class="spinner-border text-info mb-3 d-block mx-auto" id="loader" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>`;
            }
            xhr.onload = function() {
                tours_data.innerHTML = this.responseText;
            }
            xhr.send();
        }

        window.onload = function() {
            fetch_tours();
        }

        function name_search_filter() {
            if (search_name.value != '') {
                fetch_tours();
                name_search_btn.classList.remove('d-none');
            } else {
                name_search_clear();
            }
        }

        function name_search_clear() {
            search_name.value = '';
            name_search_btn.classList.add('d-none');
            fetch_tours();
        }
    </script>

    <?php require('Inc/footer.php'); ?>

</body>

</html>