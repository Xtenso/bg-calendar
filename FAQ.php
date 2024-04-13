<?php
$pageTitle = "ЧЗВ - Често задавани въпроси";
$additionalStyling = 'FAQ';
$currentURL = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$metaDescription = "Отговори на често задавани въпроси относно българския календар, официалните празници, именните дни и важните събития в България.";
include 'header.php';
?>

<!--Search field-->
<input type="text" id="FAQSearch" placeholder="Търсете тук">

<!--Custom accordions-->
<div id="accordionContainer">
    <div class="accordion-item">
        <button class="accordion-button" onclick="toggleAccordion('firstAccordion')">
            Какво става ако официален празник се пада уикенда?
        </button>
        <div id="firstAccordion" class="accordion-content">
            <div class="accordion-body">
                Ако официален празник се пада уикенда, то той се премества на следващия работен ден. Така например, ако 3-ти март е неделя, то следващия работен ден, в случая понеделеник, става почивен.
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <button class="accordion-button" onclick="toggleAccordion('secondAccordion')">
            Какво показва графиката "Почивни Дни"?
        </button>
        <div id="secondAccordion" class="accordion-content">
            <div class="accordion-body">
                Броя почивни дни написан в графиката включва всички официални празници без уикендите. Така например, ако 3-ти март е събота, то той няма да бъде включен в графиката, освен ако почивен не стане следващия работен ден, както е в случая с 3-ти март.<br><br>
                Броя неучебни дни НЕ включва официални празници, уикенди, както и неучебни дни и ваканции от следващата учебна година, ако те все още не са били обявени от МОН (Министерство на Образованието и Науката). Това обикновенно става Септември месец. </div>
        </div>
    </div>
    <div class="accordion-item">
        <button class="accordion-button" onclick="toggleAccordion('thirdAccordion')">
            Къде мога да открия всички Именни Дни на дадено име?
        </button>
        <div id="thirdAccordion" class="accordion-content">
            <div class="accordion-body">
                За да откриете кога дадено име празнува имен ден, трябва да отидете на страницата "Именни Дни" и в полето "Търсене по име", да запишете името, което ви интересува. След това ще се появи списък с опции, от който можете да изберете една.
                Ако името случайно не се появи в списъка, моля свържете се с нас, като ни изпратите информация за празника и датата, чрез формата за контакти, която се намира в края на страницата, и ние ще се постараем да го добавим възможно най-бързо.
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <button class="accordion-button" onclick="toggleAccordion('fourthAccordion')">
            Custom Accordion Item #1
        </button>
        <div id="fourthAccordion" class="accordion-content">
            <div class="accordion-body">
                Content for item #1.
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <button class="accordion-button" onclick="toggleAccordion('fifthAccordion')">
            Custom Accordion Item #1
        </button>
        <div id="fifthAccordion" class="accordion-content">
            <div class="accordion-body">
                Content for item #1.
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <button class="accordion-button" onclick="toggleAccordion('sixthAccordion')">
            Custom Accordion Item #1
        </button>
        <div id="sixthAccordion" class="accordion-content">
            <div class="accordion-body">
                Content for item #1.
            </div>
        </div>
    </div>
</div>

<!-- No results message -->
<div id="noResults" style="display: none;">
    <p>За съжаление няма резултати които да отговарят на вашето търсене!</p>
    <p>Пробвайте да търсите с други термини. Ако все още имате въпроси, не се колебайте да се свържете с нас!</p>
</div>


<script>
    document.getElementById("FAQSearch").addEventListener("input", function() {
        var searchText = this.value.toLowerCase();
        var accordionItems = document.querySelectorAll(".accordion-item");
        var noResults = document.getElementById("noResults");
        var found = false;

        accordionItems.forEach(function(item) {
            var button = item.querySelector(".accordion-button");
            var content = item.querySelector(".accordion-body");
            if (button.innerText.toLowerCase().includes(searchText) || content.innerText.toLowerCase().includes(searchText)) {
                item.style.display = "block";
                found = true;
            } else {
                item.style.display = "none";
            }
        });

        // Show "no results" message if no matching items were found
        if (!found) {
            noResults.style.display = "block";
        } else {
            noResults.style.display = "none";
        }
    });
</script>
<?php
include 'footer.php';
?>