body {
	font-family: Arial, Helvetica, sans-serif;
}
.myProgress {
  position: relative;
  width: 100%;
  height: 30px;
  background-color: #ddd;
}

.myBar {
  position: absolute;
  width: 0%;
  height: 100%;
  background-color: #4CAF50;
}

.label {
  text-align: center;
  line-height: 30px;
  color: white;
}

.status {
	float: right;
	font-size: 0.8em;
}

.contentbox {
	float:left;
	background:#f7f7f7;
	margin:0;
	min-width: 430px;
	max-width: 600px;
	width: 46%;
	min-height:300px;
	margin:15px;
	border-color: #000000;
	border-width: 1px;
	border-left-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-bottom-left-radius: 50px;
	border-bottom-right-radius: 50px;
	border-top-left-radius: 50px;
	border-top-right-radius: 10px;
	box-shadow: 2px 2px 5px #888888;
}

.wide {
	min-width: 800px;
	max-width: 1900px; 
	width: 96%;
}

.contentbox h2 {
	background: #000000;
	color: #ffffff;
	padding 15px;
	height: 50px;
	line-height: 50px;
	text-align: center;
	text-decoration: none;
	vertical-align: middle;
	margin: 0;
	border-top-left-radius: 48px;
	border-top-right-radius: 10px;
}

.contentbox h2 a {
	color: #ffffff;
	text-decoration: none;
}



.contentbox .content {
	margin: 5px;
	margin-bottom: 40px;
}

.contentbox .content ul {
	list-style: none;
	padding-left: 2px;
}

.contentbox .content li {
	line-height: 2em;
}
.contentbox .content tr {
	border: thin solid #000000;
}
.contentbox .content tr:nth-child(odd) {
	background-color: #f7f7f7;
}
.contentbox .content tr:nth-child(even) {
	background-color: #ffffff;
}

.button {
        display: inline-block;
        zoom: 1; /* zoom and *display = ie7 hack for display:inline-block */
        *display: inline;
        vertical-align: baseline;
        margin: 0 2px;
        outline: none;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        font-size: 0.8em;
        padding: .5em 2em .55em;
        text-shadow: 0 1px 1px rgba(0,0,0,.3);
	border-bottom-left-radius: 2px;
	border-bottom-right-radius: 10px;
	border-top-left-radius: 10px;
	border-top-right-radius: 10px;
        box-shadow: 1px 1px 3px #888888;
}

.button:hover, .button:focus {
        text-decoration: none;
}
.button:active {
        position: relative;
        top: 1px;
}

.red {
        background: #000000;
        color: #ffffff;
	border-color: #ed1c24;
	border-width: 1px;
	border-style: solid;
}

.grey {
        background: #8b8878;
        color: #000000;
	border-color: #ed1c24;
	border-width: 1px;
	border-style: solid;
}



.red:hover, .red:focus {
        color: #ed1c24;
        background: #ffffff;
}

.grey:hover, .grey:focus {
        color: #ed1c24;
        background: #8b8878;
}


.red:active {
        background: #ffffff;
        color: #ed1c24;
}


footer {
	margin-top: 1.5em;
	clear: both;
}

