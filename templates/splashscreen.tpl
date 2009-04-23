<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="nb" xml:lang="nb">
<head>
<title>Aether Splashscreen ({$aether.providers.title})</title>
<!-- Color palette
Base: C8E3FF
Various colours that fits:
#C7E2FF
#C8C7FF
#E4C7FF
#FFC7FE
#C7FEFF
#8AC2FF
#4DA3FF
#FFC7E2
#C7FFE4
#FFA94D
#FFC68A
#FFC8C7
#C7FFC8
#E2FFC7
#FEFFC7
#FFE4C7
-->
<style>
* {
    margin: 0;
    padding: 0;
}
body {
    background-color: #FFE4C7;
    font-family: Times New Roman, Trebuchet, sans-serif;
    font-size: 12px;
}
div {
    margin: 0 auto;
    margin-top: 5em;
    width: 80%;
    padding: 2.6em;
    background-color: #8AC2FF;
    border: 1px solid #4DA3FF;
}
div h1 {
    font-size: 3em;
    color: #555;
    border-bottom: 1px solid #444;
}
div h2 {
    font-size: 2em;
    color: #555;
    margin: 1em 0 .3em 0;
}
div p {
    margin-top: .5em;
    font-size: 1.6em;
    color: #fff;
}
</style>
</head>
<body>
<div>
<h1>Welcome to Aether ({$options.version})</h1>
<p>You have now correctly installed the Aether framework. Some documentation can be <a href="https://drift.hardware.no:82/wiki/index.php/Aether">located here</a>.</p>
<h2>Imported and provided</h2>
<p>The following text is served by a module imported through the <strong>import</strong> statement, and provided using
provides="foo" before its included using $aether.providers.extra. Just to showcase that everything works.</p>
<p><em>{$aether.providers.title}</em></p>
<h2>$options['additive']</h2>
<p>{$options.additive}</p>
</div>
</body>
</html>
