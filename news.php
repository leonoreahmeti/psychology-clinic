<?php

require_once __DIR__ . '/config/database.php';


/*
|--------------------------------------------------------------------------
| LOAD PUBLISHED NEWS
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT
            id,
            title,
            content,
            image,
            created_at
        FROM news
        WHERE status = 'published'
        ORDER BY created_at DESC
    ");

    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $news = [];

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>News & Updates - Nafie Sylejmani</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

    <style>

        /*
        |--------------------------------------------------------------------------
        | NEWS PAGE
        |--------------------------------------------------------------------------
        */

        .news-section {
            max-width: 1100px;
            margin: 0 auto;
            padding: 70px 25px;
        }

        .news-header {
            text-align: center;
            margin-bottom: 45px;
        }

        .news-header h1 {
            font-size: 38px;
            color: #243746;
            margin-bottom: 10px;
        }

        .news-header p {
            color: #6b777f;
            font-size: 15px;
        }


        /*
        |--------------------------------------------------------------------------
        | POSTS
        |--------------------------------------------------------------------------
        */

        .posts {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .post-card {
            background: white;
            border-radius: 18px;
            padding: 28px;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.07);

            border: 1px solid #edf1ef;
        }

        .post-card h3 {
            font-size: 24px;
            color: #243746;
            margin-bottom: 7px;
        }

        .post-date {
            color: #8a959b;
            font-size: 12px;
            margin-bottom: 18px;
        }

        .post-card > img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            display: block;

            border-radius: 14px;

            margin-bottom: 20px;
        }

        .post-content {
            color: #52616b;
            font-size: 14px;
            line-height: 1.8;
            white-space: normal;
        }


        /*
        |--------------------------------------------------------------------------
        | COMMENTS
        |--------------------------------------------------------------------------
        */

        .comments {
            margin-top: 25px;
            padding-top: 20px;

            border-top: 1px solid #edf1ef;
        }

        .comments-title {
            font-size: 15px;
            font-weight: 600;

            color: #243746;

            margin-bottom: 15px;
        }

        .comment {
            background: #f6f9f7;

            border-radius: 10px;

            padding: 12px 14px;

            margin-bottom: 10px;
        }

        .comment strong {
            color: #243746;
            font-size: 13px;
        }

        .comment p {
            color: #5e6b72;
            font-size: 13px;
            line-height: 1.6;

            margin-top: 4px;
        }


        /*
        |--------------------------------------------------------------------------
        | COMMENT FORM
        |--------------------------------------------------------------------------
        */

        .comment-form {
            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 10px;

            margin-top: 20px;
        }

        .comment-form input {
            width: 100%;

            border: 1px solid #dfe7e3;

            border-radius: 9px;

            padding: 11px 13px;

            font-family: 'Poppins', sans-serif;

            font-size: 13px;

            outline: none;

            color: #243746;
        }

        .comment-form input:focus {
            border-color: #6fcf97;
        }

        .comment-form input[name="comment"] {
            grid-column: 1 / -1;
        }

        .comment-form button {
            grid-column: 1 / -1;

            width: fit-content;

            border: none;

            background: #6fcf97;

            color: white;

            padding: 10px 18px;

            border-radius: 9px;

            font-family: 'Poppins', sans-serif;

            font-size: 13px;

            font-weight: 600;

            cursor: pointer;
        }

        .comment-form button:hover {
            background: #57b87e;
        }


        /*
        |--------------------------------------------------------------------------
        | NO NEWS
        |--------------------------------------------------------------------------
        */

        .no-news {
            background: white;

            border-radius: 18px;

            padding: 50px 25px;

            text-align: center;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.06);

            color: #6b777f;
        }

        .no-news h3 {
            color: #243746;

            margin-bottom: 8px;
        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 700px) {

            .news-section {
                padding: 45px 18px;
            }

            .news-header h1 {
                font-size: 30px;
            }

            .post-card {
                padding: 20px;
            }

            .post-card h3 {
                font-size: 21px;
            }

            .post-card > img {
                max-height: 350px;
            }

            .comment-form {
                grid-template-columns: 1fr;
            }

            .comment-form input[name="comment"] {
                grid-column: auto;
            }

            .comment-form button {
                grid-column: auto;
                width: 100%;
            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav>

    <div class="logo">

        <a href="index.html">

            <img
                src="images/logo.jpeg"
                alt="Logo"
            >

        </a>

    </div>


    <button
        class="hamburger"
        id="hamburger"
        aria-label="Open menu"
    >

        <span></span>
        <span></span>
        <span></span>

    </button>


    <ul id="nav-menu">

        <li>
            <a href="index.html">
                Home
            </a>
        </li>

        <li>
            <a href="about.html">
                About
            </a>
        </li>

        <li>
            <a href="services.html">
                Services
            </a>
        </li>

        <li>
            <a href="contact.html">
                Contact
            </a>
        </li>

        <li>
            <a href="booking.html">
                Booking
            </a>
        </li>
        
        <li class="language-switcher">

            <button
                type="button"
                id="languageButton"
                class="language-button"
            >
                🇬🇧 EN
            </button>

            <div
                id="languageMenu"
                class="language-menu"
            >

                <button
                    type="button"
                    class="language-option"
                    data-lang="en"
                >
                    🇬🇧 English
                </button>

                <button
                    type="button"
                    class="language-option"
                    data-lang="sq"
                >
                    🇦🇱 Shqip
                </button>

            </div>

        </li>

    </ul>

</nav>



<!-- =====================================================
     NEWS SECTION
===================================================== -->

<section class="news-section">


    <div class="news-header">

        <h1>
            Latest Updates
        </h1>

        <p>
            Stay informed about workshops, events, and tips
        </p>

    </div>



    <!-- =================================================
         POSTS
    ================================================== -->

    <div class="posts">


        <?php if (empty($news)): ?>


            <div class="no-news">

                <h3>
                    No news available
                </h3>

                <p>
                    There are currently no published news or updates.
                </p>

            </div>


        <?php else: ?>


            <?php foreach ($news as $post): ?>


                <article class="post-card">


                    <!-- TITLE -->

                    <h3>

                        <?= htmlspecialchars(
                            $post['title']
                        ) ?>

                    </h3>


                    <!-- DATE -->

                    <div class="post-date">

                        <?= date(
                            'F j, Y',
                            strtotime($post['created_at'])
                        ) ?>

                    </div>


                    <!-- IMAGE -->

                    <?php if (!empty($post['image'])): ?>

                        <img
                            src="<?= htmlspecialchars(
                                $post['image']
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                $post['title']
                            ) ?>"
                        >

                    <?php endif; ?>


                    <!-- CONTENT -->

                    <div class="post-content">

                        <?= nl2br(
                            htmlspecialchars(
                                $post['content']
                            )
                        ) ?>

                    </div>



                    <!-- =================================================
                         COMMENTS
                    ================================================== -->

                    <?php

                    try {

                        $commentStmt = $pdo->prepare("
                            SELECT
                                name,
                                comment,
                                created_at
                            FROM news_comments
                            WHERE news_id = :news_id
                            AND status = 'approved'
                            ORDER BY created_at ASC
                        ");

                        $commentStmt->execute([
                            ':news_id' => $post['id']
                        ]);

                        $comments =
                            $commentStmt->fetchAll(PDO::FETCH_ASSOC);

                    } catch (PDOException $e) {

                        $comments = [];

                    }

                    ?>


                    <div class="comments">


                        <div class="comments-title">

                            Comments

                        </div>


                        <?php if (!empty($comments)): ?>


                            <?php foreach ($comments as $comment): ?>


                                <div class="comment">

                                    <strong>

                                        <?= htmlspecialchars(
                                            $comment['name']
                                        ) ?>

                                    </strong>

                                    <p>

                                        <?= nl2br(
                                            htmlspecialchars(
                                                $comment['comment']
                                            )
                                        ) ?>

                                    </p>

                                </div>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <p
                                style="
                                    color:#8a959b;
                                    font-size:13px;
                                "
                            >
                                No comments yet.
                            </p>


                        <?php endif; ?>



                        <!-- =================================================
                             ADD COMMENT
                        ================================================== -->

                        <form
                            action="submit_comment.php"
                            method="POST"
                            class="comment-form"
                        >


                            <input
                                type="hidden"
                                name="news_id"
                                value="<?= (int) $post['id'] ?>"
                            >


                            <input
                                type="text"
                                name="name"
                                placeholder="Your name"
                                required
                            >


                            <input
                                type="email"
                                name="email"
                                placeholder="Your email"
                                required
                            >


                            <input
                                type="text"
                                name="comment"
                                placeholder="Write a comment"
                                required
                            >


                            <button type="submit">

                                Comment

                            </button>


                        </form>


                    </div>


                </article>


            <?php endforeach; ?>


        <?php endif; ?>


    </div>


</section>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer>

    <p>
        © 2026 Psychology Clinic | All rights reserved
    </p>

</footer>



<!-- =====================================================
     HAMBURGER SCRIPT
===================================================== -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const hamburger =
            document.getElementById('hamburger');

        const navMenu =
            document.getElementById('nav-menu');


        if (hamburger && navMenu) {

            hamburger.addEventListener(
                'click',
                function () {

                    navMenu.classList.toggle('active');

                    hamburger.classList.toggle('active');

                }
            );

        }

    }
);

</script>
<div id="google_translate_element"></div>

<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script src="language.js"></script>


</body>

</html>
