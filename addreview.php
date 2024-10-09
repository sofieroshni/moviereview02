<?php
const CONFIG = [
    'db' => 'mysql:dbname=moviereview;host=linux93.unoeuro.com;port=3306',
    'db_user' => 'root',
    'db_password' => '',
];

global $pdo;

try {

    $pdo = new PDO(CONFIG['db'], CONFIG['db_user'], CONFIG['db_password']);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Forbindelsen mislykkedes: " . $e->getMessage();
    exit;

// Definerer sql()-funktionen
    function sql($query, $params = []) {
        global $pdo;

        try {
            $stmt = $pdo->prepare($query);

            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo "Forespørgslen mislykkedes: " . $e->getMessage();
            return [];
        }
    }}
//$con sikrer forbindelse $query_one_reviews,$query_two_movies,$query_three_reviewers
// indsætter med sql det brugeren skriver i inputfeltet når det laver et review, det ene "udløser det andet",
// og den sidste $query sørger for at der sker en connection imellem de tre tabeller
// i movie_actor. Det hele kører ved at ved submit,
// så bliver brugerens input sendt til databasen og de følgende querys kører;

$con = mysqli_connect("localhost", "root", "", "moviereview");
if ($con === false) {
    die("Connection fejl: " . mysqli_connect_error());
}
//laver en varibabel og connecter til min database. Derefter laver jeg en betingelse: hvis min
if (isset($_POST['submit'])) {
    $movieTitle = $_POST['movieTitle'];
    $review_title = $_POST['review_title'];
    $underubrik = $_POST['underubrik'];
    $review_text = $_POST['review_text'];
    $review_rating = $_POST['review_rating'];
    $review_date = $_POST['review_date'];
    $name_of_the_reviewer = $_POST['name_of_the_reviewer'];

    $query_one_reviews = mysqli_query($con, "INSERT INTO reviews(reviewId, review_title, underubrik, review_text, review_rating, review_date) 
        VALUES (NULL, '$review_title', '$underubrik', '$review_text', '$review_rating', '$review_date')");

    if ($query_one_reviews) {
        $reviewId = mysqli_insert_id($con);

        $query_two_movies = mysqli_query($con, "INSERT INTO movies(movieTitle, movieId) VALUES ('$movieTitle', NULL)");

        if ($query_two_movies) {
            $movieId = mysqli_insert_id($con);
            $query_three_reviewer = mysqli_query($con, "INSERT INTO reviewers(name_of_the_reviewer) VALUES ('$name_of_the_reviewer')");

            if ($query_three_reviewer) {
                $reviewerId = mysqli_insert_id($con);
                $query_four_con = mysqli_query($con, "INSERT INTO movie_actor(movieConId, reviewConId, reviewerConId) VALUES ('$movieId', '$reviewId', '$reviewerId')");

                if ($query_four_con) {
                    echo "<script>
                        alert('Anmeldelse er postet!');
                        window.location.href = 'my_reviews.php';
                    </script>";
                } else {
                    echo "<script>
                        alert('Fejl i oprettelsen af forbindelse mellem film og anmeldelse');
                    </script>";
                }
            }
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        form[method="post"] {
            width: 50vw;
            height: 500px;
            justify-content: center;
            align-items: center;
        }
        form {
            margin-top: 10vh;
        }
        input[type="date"],
        input[type="text"],
        input[type="file"],
        textarea {
            width: 400px;
            padding: 10px;
            border: none;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
            transition: all 0.2s ease-in-out;
            font-family: Inter, serif;
        }
        textarea {
            height: 300px;
        }
        .star-rating .bi-star-fill {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
        }
        .star-rating .bi-star-fill.checked {
            color: gold;
        }

        @media (min-width: 900px) {
            input[type="date"],
            input[type="text"],
            input[type="file"],
            textarea {
                width: 800px;
                padding: 10px;
                border: none;
                border-bottom: 1px solid #ddd;
                font-size: 16px;
                transition: all 0.2s ease-in-out;
                font-family: Inter, serif;
            }
            .submit{
                margin-bottom: 10vh;
              align-self: end;justify-self: flex-end;
                margin-top: 32px;
            }
            .submit:hover{
                background-color: white;
                color: black;
            }

        }
    </style>
    <title>Movie Review</title>
</head>
<?php include "header.php"; ?>
<body>
<main>
    <form method="POST">
        <label>
            <input type="text" name="movieTitle" placeholder="Filmtitel">
        </label><br>
        <label>
            <input type="text" name="name_of_the_reviewer" placeholder="Skriv dit navn">
        </label><br>
        <label>
            <input type="text" name="review_title" placeholder="Indtast anmeldelsestitel">
        </label><br>
        <label>
            <input type="text" name="underubrik" placeholder="Skriv en underrubrik">
        </label><br>
        <label>
            <textarea type="text" name="review_text" placeholder="Skriv din anmeldelse her"></textarea>
        </label><br>
        <label>
            <input type="date" name="review_date">
        </label><br>

        <!-- Bootstrap Star Rating System -->
        <label>
            <div class="star-rating">
                <i class="bi bi-star-fill" data-rating="1"></i>
                <i class="bi bi-star-fill" data-rating="2"></i>
                <i class="bi bi-star-fill" data-rating="3"></i>
                <i class="bi bi-star-fill" data-rating="4"></i>
                <i class="bi bi-star-fill" data-rating="5"></i>
            </div>
            <input type="hidden" name="review_rating" id="review_rating" required>
        </label><br>

        <button class="submit pnormal" type="submit" name="submit">Indsend</button>
    </form>
</main>

<script>
    const stars = document.querySelectorAll('.star-rating .bi-star-fill');
    const ratingInput = document.getElementById('review_rating');

    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            stars.forEach(star => star.classList.remove('checked'));

            for (let i = 0; i <= index; i++) {
                stars[i].classList.add('checked');
            }

            ratingInput.value = index + 1;
        });
    });
    //laver en anynonym funktion, men en eventlistener til min stjerne(click)
    //stars.forEach er et loop som kører en funktion for hvart element i min liste(boostrapstjernere)
    //parametrene star,index (star er boostrapstjerrner) index er "positionen af stjerne" (0, 1 eller 4)
    // i = 0, I<=INDEX; i kører op til index-af stjerner(antallet),
    //klikker bruger på 3.stjerne i=2.
    // let i = 0;
    //1++; i er nu 1
    // i++ i er nu 2 værdien i øget med en 1 hver gang.
    //FORM VALUE: rating sendes ind i input. og inputvaluen sendes via min betingelse if($_POST){}
    //

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
