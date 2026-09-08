(function () {

    // ================================
    // GOOGLE TRANSLATE INITIALIZATION
    // ================================

    window.googleTranslateElementInit = function () {

        new google.translate.TranslateElement(
            {
                pageLanguage: "en",
                includedLanguages: "en,sq",
                autoDisplay: false
            },
            "google_translate_element"
        );

    };


    // ================================
    // CHANGE LANGUAGE
    // ================================

    function changeLanguage(language) {

        const select = document.querySelector(".goog-te-combo");

        if (!select) {
            console.log("Google Translate is not ready yet.");

            // Try again after Google Translate loads
            setTimeout(function () {
                changeLanguage(language);
            }, 500);

            return;
        }

        select.value = language;

        select.dispatchEvent(new Event("change"));

        // Save selected language
        localStorage.setItem("siteLanguage", language);

        updateLanguageButton(language);
    }


    // ================================
    // UPDATE BUTTON
    // ================================

    function updateLanguageButton(language) {

        const languageButton =
            document.getElementById("languageButton");

        if (!languageButton) {
            return;
        }

        if (language === "sq") {

            languageButton.textContent = "🇦🇱 SQ";

        } else {

            languageButton.textContent = "🇬🇧 EN";

        }

    }


    // ================================
    // LANGUAGE MENU
    // ================================

    function initializeLanguageMenu() {

        const languageButton =
            document.getElementById("languageButton");

        const languageMenu =
            document.getElementById("languageMenu");


        if (!languageButton || !languageMenu) {
            return;
        }


        // Open / close menu
        languageButton.addEventListener(
            "click",
            function (event) {

                event.stopPropagation();

                languageMenu.classList.toggle("show");

            }
        );


        // Language buttons
        document
            .querySelectorAll(".language-option")
            .forEach(function (button) {

                button.addEventListener(
                    "click",
                    function (event) {

                        event.stopPropagation();

                        const language =
                            button.getAttribute("data-lang");

                        changeLanguage(language);

                        languageMenu.classList.remove("show");

                    }
                );

            });


        // Close menu when clicking outside
        document.addEventListener(
            "click",
            function () {

                languageMenu.classList.remove("show");

            }
        );

    }


    // ================================
    // RESTORE SAVED LANGUAGE
    // ================================

    function restoreLanguage() {

        const savedLanguage =
            localStorage.getItem("siteLanguage");

        if (!savedLanguage) {
            return;
        }

        updateLanguageButton(savedLanguage);


        // Wait for Google Translate
        setTimeout(function () {

            const select =
                document.querySelector(".goog-te-combo");

            if (!select) {
                return;
            }

            select.value = savedLanguage;

            select.dispatchEvent(
                new Event("change")
            );

        }, 1000);

    }


    // ================================
    // PAGE READY
    // ================================

    document.addEventListener(
        "DOMContentLoaded",
        function () {

            initializeLanguageMenu();

            restoreLanguage();

        }
    );

})();
