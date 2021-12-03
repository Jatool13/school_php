<?php

// Creating the PDO connection to MySQL
$dsn = "mysql:host=localhost;port=3306;dbname=school_php;Charset=UTF-8";
$dbUser = "root";
$dbPassword = "";
$dbOptions = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
$db = new PDO($dsn, $dbUser, $dbPassword, $dbOptions);

// Counting the number of records inside the students table
$sql = "SELECT COUNT(*) AS count FROM students";
$count = $db->query($sql)->fetch(PDO::FETCH_ASSOC)["count"];

if ($count == 0 && !isset($_GET['order'])) {
    // Deleting the previous records in the students table
    $sql = "TRUNCATE TABLE students";
    $db->exec($sql);

    // Inserting new records inside the students table
    $name = "";
    $mark = 0;
    $address = "Lot II E 45 IS Ankadindramamy ";
    $sql = "INSERT INTO students (name, mark, address) VALUES (:name, :mark, :address)";
    $statement = $db->prepare($sql);
    $statement->bindParam(":name", $name);
    $statement->bindParam(":mark", $mark);
    $statement->bindParam(":address", $address);
    for ($i = 0; $i < 100; $i++) {
        $name = "Student $i";
        $mark = rand(10, 20);
        $statement->execute();
    }
}

// Getting all the students
$order = $_GET['order'] ?? 'asc';
$page = $_GET['page'] ?? '1';
$limit = 10;
$sortBy = $_GET['sortBy'] ?? 'id';
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM students WHERE name LIKE " . "'%$search%'" ." ORDER BY $sortBy $order LIMIT $limit OFFSET " . (($page - 1) * $limit);
$statement  = $db->query($sql);
$students = $statement->fetchAll(PDO::FETCH_ASSOC);

// Check sort option
function checkSort($sortBy, $_sortBy, $order, $_order) {
    if ($sortBy == $_sortBy && $order == $_order) return 'checked';
    else return '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School</title>
    <link rel="stylesheet" href="static/bootstrap-4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/fontawesome-free-5.12.1-web/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center pt-2 pb-4">Liste des élèves</h2>
        <form action="http://localhost/school_php" method="GET">
            <input type="hidden" name="order" value="<?= $order ?>">
            <input type="hidden" name="sortBy" value="<?= $sortBy ?>">
            <div class="form-group d-flex">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control" value="<?= $search ?>" placeholder="Chercher un étudiant par nom" <?= (empty($search) ? "" : "autofocus") ?> >
                </div>
                <button class="btn btn-primary ml-2">Search</button>
            </div>
            <table class="table table-bordered table-hover table-striped" >
                <thead class="thead-dark" >
                    <tr>
                        <th>
                            <div class="d-flex justify-content-between">
                                <span class="col-title">Id</span>
                                <div class="sort">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="sort" sort="id-asc"
                                            <?= checkSort($sortBy, "id", $order, "asc") ?>
                                            >
                                            <i class="fas fa-caret-up"></i>
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="sort" sort="id-desc"  
                                            <?= checkSort($sortBy, "id", $order, "desc") ?>>
                                            <i class="fas fa-caret-down"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex justify-content-between">
                                <span class="col-title">Nom</span>
                                <div class="sort">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="sort" sort="name-asc"
                                            <?= checkSort($sortBy, "name", $order, "asc") ?>>
                                            <i class="fas fa-caret-up"></i>
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="sort" sort="name-desc"
                                            <?= checkSort($sortBy, "name", $order, "desc") ?>>
                                            <i class="fas fa-caret-down"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex justify-content-between"><span class="col-title">Note</span>
                                <div class="sort">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="sort" sort="mark-asc"
                                            <?= checkSort($sortBy, "mark", $order, "asc") ?>>
                                            <i class="fas fa-caret-up"></i>
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="sort" sort="mark-desc"
                                            <?= checkSort($sortBy, "mark", $order, "desc") ?>>
                                            <i class="fas fa-caret-down"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th>
                            Adresse
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student) : ?>
                    <tr>
                        <td><?= $student['id'] ?></td>
                        <td><?= $student['name'] ?></td>
                        <td><?= $student['mark'] ?></td>
                        <td><?= $student['address'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <input type="hidden" name="page" value="<?= $page ?>">
            <div class="d-flex justify-content-between mb-5">
                <?php if ($page > 1) : ?>
                <button type="button" id="prev" value="prev" class="btn btn-outline-primary" >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Page précédente
                </button>
                <?php endif; ?>
                <?php if ((int) $page <= (int) ($count / $limit)) : ?>
                <button type="button" id="next" value="next" class="btn btn-outline-primary" >
                    Page suivante
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
<script>
    const pageControllers = {
        prev: document.getElementById('prev'),
        next: document.getElementById('next'),
        handlePageChange: function (e) {
            const button = e.target, form = button.form;
            if (button.value === 'prev') form.page.value--;
            else form.page.value++;
            form.submit();
        },
        init: function () {
            if (this.prev) this.prev.onclick = this.handlePageChange;
            if (this.next) this.next.onclick = this.handlePageChange;
        }
    };
    pageControllers.init();

    const sortControllers = {
        radios: document.querySelectorAll('input[type="radio"]'),
        handleSortChange: function (e) {
            const radio = e.target, form = radio.form;
            form.page.value = 1;
            const [sortBy, order] = radio.getAttribute('sort').split('-');
            form.sortBy.value = sortBy;
            form.order.value = order;
            form.submit();
        },
        init: function () {
            const n = this.radios.length;
            for (let i = 0; i < n; i++) {
                const radio = this.radios[i];
                radio.onchange = this.handleSortChange;
            }
        }
    };
    sortControllers.init();
</script>
</body>
</html>