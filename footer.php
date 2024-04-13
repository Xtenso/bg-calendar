</div>

<!-- Report form popup & modal overlay -->
<div id="modalOverlay">
	<div id="modalContent">
		<!-- Form -->
		<form id="reportForm" action="saveFormData.php" method="post">
			<h2>Форма за контакт</h2>
			<p>Ако смятате, че информацията на сайта е неточна или непълна, имате въпроси относно функционалността или имате предложения, не се колебайте да се свържете с нас, като попълните полетата по долу!</p>
			<!--Used to send over the URL of the page we are currently on, so that we can be correctly redirected after the form submission-->
			<input type="hidden" name="url" id="currentUrlInput">
			<div id="reportFormLine1">
				<input type="text" name="reportName" placeholder="Име" required>
				<select name="reportType" required>
					<option value="" disabled selected>Тип проблем</option>
					<option value="Почивен ден">Почивен ден</option>
					<option value="Имен ден">Имен ден</option>
					<option value="Проблем със сайта">Проблем със сайта</option>
					<option value="Друг проблем">Друг проблем</option>
				</select>
			</div>
			<textarea name="reportDescription" placeholder="Описание на проблема" required minlength="20"></textarea>
			<div id="reportFormButtons">
				<input class="custom-button" type="button" value="Отказ" id="closeButton">
				<input class="custom-button" type="submit" value="Изпрати">
			</div>
		</form>
	</div>
</div>

<!--footer-->

<footer>
	<h2>Designed and developed by Stefan Borisov</h2>
	<p>
		Този календар е разработен с идеята да събере информацията за всички важни за Българина събития на едно място. От именни и почивни дни, до специални събития и празници, тук ще намерите всичко, което ви интересува, за да организирате времето си по най-добрия начин, и да се уверите че няма да изпуснете именния ден на приятел или роднина. С помощта на различните инструменти като търсене и филтриране сме се постарали да направим информацията достъпна и винаги лесна за откриване. Нашата цел винаги е била да предоставим надеждна и актуална информация, но както всички знаем, нищо не е съвършено. Затова, ако забележите някакви неточности или пропуски, моля, помогнете ни като ни информирате за тях.
	</p>
	<button class="custom-button">Други проекти</button>
	<button id="reportButton" class="custom-button">Форма за контакт</button>
	<button class="custom-button" onclick="window.location.href = 'FAQ.php';">Често задавани въпроси</button>
</footer>
</div>

<!--Java Script-->
<script src="scriptEnd.js"></script>
</body>

</html>