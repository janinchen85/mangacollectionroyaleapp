<!DOCTYPE html>
<html>
    
<head>
    <title>Aquaria contact form</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <meta name="”viewport”" content="”width=device-width," initial-scale="1.0”">
  </head>
  <body>
      <div class="error">{$error}</div>
      <div class="success">{$success}</div>
    <div class="container">
    	<div class="serieContainer">
            <form method="post">
            <h1>Serie hinzufügen</h1>
            <div class="rows">
                <div class="label"><label for="serieName">Name</label></div>
                <div class="input"><input type="text" name="serieName" id="serieName"></div>
            </div>
            <div class="rows">
                <div class="label"><label for="selectAuthor">Author</label></div>
                <select name="selectAuthor" id="selectAuthor"><option value=""></option>{$author}</select>
            </div>
            <div class="rows">
                <div class="label"><label for="selectPublisher">Publisher</label></div>
                <select name="selectPublisher" id="selectPublisher"><option value=""></option>{$publisher}</select>
            </div>
            <div class="rows">
                <div class="label"><label for="serieStart">Start</label></div>
                <div class="input"><input type="date" name="serieStart" id="serieStart"></div>
            </div>
            <div class="rows">
                <div class="label"><label for="serieEnd">Ende</label></div>
                <div class="input"><input type="date" name="serieEnd" id="serieEnd"></div>
            </div>
            <div class="rows">
                <div class="label"><label for="serieVolumes">Bände</label></div>
                <div class="input"><input type="number" name="serieVolumes" id="serieVolumes"></div>
            </div>
            <div class="rows">
                <div class="label"><label for="isCanceled">Abgebrochen</label></div>
                <div class="input"><input type="checkbox" name="isCanceled" id="isCanceled" value="1"></div>
            </div>
            <div class="rows">
                <div class="label"><label for="seriesDesc">Beschreibung</label></div>
                <div class="input"><textarea name="seriesDesc" id="seriesDesc" cols="30" rows="10"></textarea></div>
            </div>
            <button type="submit" name="addSerie">Hinzufügen</button>
            </form>
        </div>
        <div class="volumeContainer">
            <form method="post">
            <h1>Manga hinzufügen</h1>
            <div class="rows">
                <div class="label"><label for="selectSerie">Serie</label></div>
                <select name="selectSerie" id="selectSerie"><option value=""></option>{$serie}</select>
            </div>
            <div class="label"><label for="volumeISBN">ISBN</label></div>
            <div class="input"><input type="text" name="volumeISBN" id="volumeISBN"></div>
            <div class="label"><label for="volumeVolume">Band</label></div>
            <div class="input"><input type="number" name="volumeVolume" id="volumeVolume"></div>
            <div class="label"><label for="volumePages">Seiten</label></div>
            <div class="input"><input type="text" name="volumePages" id="volumePages"></div>
            <div class="label"><label for="volumeDate">Datum</label></div>
            <div class="input"><input type="date" name="volumeDate" id="volumeDate"></div>
            <button type="submit" name="addVolume">Hinzufügen</button>
            </form>
        </div>
        <div class="others">
            <div class="publisherContainer">
                <form method="post">
                    <h3>Publisher hinzufügen</h3>
                    <label for="publisherName">Name</label>
                    <input type="text" name="publisherName" id="publisherName">
                    <button type="submit" name="addPublisher">Hinzufügen</button>
                </form>
            </div>
            <div class="autorContainer">
                <form method="post">
                    <h3>Autor hinzufügen</h3>
                    <label for="authorName">Name</label>
                    <input type="text" name="authorName" id="authorName">
                    <button type="submit" name="addAuthor">Hinzufügen</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>