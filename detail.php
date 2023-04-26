<?php
  if(!isset($_GET['day'])) {
    header("Location: menu.php");
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jedálny lístok</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.4/datatables.min.css"/>
  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.4/datatables.min.js"></script>
  <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
  <header>
		<nav>
			<ul>
        <li><a href="menu.php">Jedálny lístok</a></li>
				<li><a href="swagger.html">Popis API metód</a></li>
        <li><a href="verification.php">Overenie API metód</a></li>
			</ul>
		</nav>
	</header>
  <div class="container">
    <h1>Jedálny lístok</h1>
    <div class="buttons">
    <a href="menu.php" class="button">Celý týždeň</a>
    <a href="detail.php?day=1" class="button">Pondelok</a>
    <a href="detail.php?day=2" class="button">Utorok</a>
    <a href="detail.php?day=3" class="button">Streda</a>
    <a href="detail.php?day=4" class="button">Štvrtok</a>
    <a href="detail.php?day=5" class="button">Piatok</a>
    <a href="detail.php?day=6" class="button">Sobota</a>
    <a href="detail.php?day=7" class="button">Nedeľa</a>
    </div>
    <div class="tableContainer">
      <h2 id="day"> <span id="date"></span></h2>
      <table id="data-table" class="table">
        <thead>
          <tr><th>Názov</th><th>Cena</th><th>Reštaurácia</th><th>Obrázok</th></tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.4/axios.min.js"></script>
  <script>
    getData();

    async function getData() {
        const id = <?=$_GET['day']?>;
        const res = await axios.get(`https://site29.webte.fei.stuba.sk/restaurants/api?day=${id}`)
        .then(function (response) {
          // Extract the data from the API response
          const data = response.data;

          // Loop through the data and dynamically create table rows and cells
          data.forEach(function (item) {
            let table;
            let date;
            let day;
            table = document.getElementById('data-table');
            day = document.getElementById('day');

            switch(item.day) {
              case "1": 
                day.innerHTML = "Pondelok";
                break;
              case "2": 
                day.innerHTML = "Utorok";
                break;
              case "3": 
                day.innerHTML = "Streda";
                break;
              case "4": 
                day.innerHTML = "Švrtok";
                break;
              case "5": 
                day.innerHTML = "Piatok";
                break;
              case "6": 
                day.innerHTML = "Sobota";
                break;
              case "7": 
                day.innerHTML = "Nedeľa";
                break;
            }

            const row = table.insertRow(-1);
            const cell1 = row.insertCell(0);
            const cell2 = row.insertCell(1);
            const cell3 = row.insertCell(2);
            const cell4 = row.insertCell(3);

            cell1.innerHTML = item.name;
            cell2.innerHTML = item.price;
            cell3.innerHTML = item.place;
            if(item.picture) {
              cell4.innerHTML = '<img src="' + item.picture + '">';
            }
          });
        })
        .catch(function (error) {
          console.log(error);
        });;
        //console.log(res.data)
        const items = res.data;
        const ol = document.getElementById('name');
        let string = '';

        ol.innerHTML = "<tr> <td></td> <td ></td> <td></td> <td></td> </tr>";

    }
  </script>
</body>
</html>
