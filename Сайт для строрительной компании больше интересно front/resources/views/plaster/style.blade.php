<!-- favicon -->
<link rel="SHORTCUT ICON" href="{{url('/favicon.ico')}}" type="image/x-icon">

<!-- Fonts -->
<link href = "{{url('/data/fonts/arial/stylesheet.css')}}" rel = "stylesheet" type = "text/css" />
<link href = "{{url('/data/fonts/gilroy/stylesheet.css')}}" rel = "stylesheet" type = "text/css" />

<!-- Font Awesome -->
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

<!-- MDB -->
<link href="{{url('/data/mdb/css/mdb.min.css')}}" rel="stylesheet"/>
<link rel="preload" href="{{url('/data/style/css/library/multi-carousel.min.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">

<!-- animate -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/> -->

<!-- my css -->
<style type="text/css">
    /*************************************/
    /***************error*****************/
    /*************************************/
    @media screen and (max-width: 576px) {
        #error .alert {
            width: 80% !important;
            left: 10% !important;
            transform: translate(0%) !important;
        }
    }

	/*************************************/
	/***************vars******************/
	/*************************************/
		:root{
			--color_default: linear-gradient(269.87deg, #FC8F49 1.22%, #EE6466 99.9%);
			--color-black: #1C1C1D;
		}


	/*************************************/
	/***************defalt****************/
	/*************************************/
		h1, h2, h3, h4, h5, p{
			margin: 0;
			padding: 0;
		}

		.btn,
		button,
		input{
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
		}

		.btn:focus, button:focus{
			outline: none;
		}

		.default_color{
			background: var(--color_default);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
		}

		.underline{
			height: 9px;
			width: 100%;
			display: block;
			background: var(--color_default);
			border-radius: 10px;
		}

        body{
            padding-right: 0px !important;
        }


	/*************************************/
	/************first block**************/
	/*************************************/
    #first_block .phone-header-mobile{
        position: absolute;
        margin-right: 30px;
        font-family: 'Arial';
        font-style: normal;
        font-weight: 700;
        font-size: 18px;
        line-height: 21px;
        color: #646464;
        margin-top: 1px;
    }

    #first_block .shining{
        background: rgba(251, 140, 76, 0.76);
        filter: blur(413px);
        width: 413px;
        position: absolute;
        height: 413px;
        left: -95px;
        right: 0;
        margin: auto;
        top: -350px;
    }

    #first_block .fbMargin{
        margin: 5rem 0;
    }

	#first_block .navbar-light .navbar-toggler {
		color: rgb(242, 113, 93);
		padding: .25rem 0rem;
	}

	#first_block{
		background-color: var(--color-black);
	}

	#first_block .logo_svg{
		margin: -.5rem 10rem 0 10rem;
	}

	#first_block #logo_mobile .logo_svg{
		display: none;
	}

	/* nav style */
    #first_block #navbarSupportedContent > a{
        font-family: 'Arial';
        font-style: normal;
        font-weight: 700;
        font-size: 18px;
        line-height: 21px;
        width: 100%;
        display: block;
        text-align: center;
        margin-top: 2rem;
    }
    #first_block #navbarSupportedContent > .btn-warning{
        width: 100%;
        border-radius: 52px;
        margin: 1rem 0;
    }

    #first_block .navbar_fixed{
		transition: .3s;
		position: fixed;
		width: 100%;
		z-index: 1100;
		padding: 1.5rem 0 1rem;
		box-shadow: 0 0 10px 0 #0009;
		opacity: 0.9;
	}

	#first_block nav{
		transition: .3s;
		font-family: 'Arial';
		font-size: 14px;
		box-shadow: none;
		background-color: var(--color-black) !important;
		padding-top: 2rem;
	}
	#first_block nav .navbar-nav a{
		color: white;
        font-size: 14px;
	}
    .navbar-expand-lg .navbar-nav .nav-item .nav-link{
        padding-right: 1.1rem;
        padding-left: 1.1rem;
    }
    .navbar-expand-lg .navbar-nav .nav-item:first-child .nav-link{
        padding-left: 0px;
    }
    .navbar-expand-lg .navbar-nav .nav-item:last-child .nav-link{
        padding-right: 0px;
    }
	#first_block nav .navbar-nav a:hover{
		transition: .3s;
		background: var(--color_default);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
	}

	/* Yandex style */
	#first_block .left_first_block > div:nth-child(1) > div:nth-child(1) > span{
		font-family: 'Gilroy';
		font-size: 12px;
		color: rgba(255, 255, 255, 0.26);
		position: absolute;
	}
	/* map style */
	#first_block .left_first_block > div:nth-child(1) > div:nth-child(2) > span,
	#first_block .map_link > span{
		font-family: 'Gilroy';
		font-size: 16px;
		line-height: 20px;
		font-weight: 900;
		position: absolute;
		margin-top: -.3rem;
	}
	#first_block .map_link > span > a{
		color: white;
		text-decoration: underline;
	}
    #first_block .map_link > span > a:after{
        content: "";
        width: 70px;
        height: 70px;
        background: #D9D9D9;
        opacity: 0.66;
        filter: blur(27px);
        display: block;
        margin-top: -50px;
        margin-left: 27px;
        animation: blinker 1.7s cubic-bezier(.5, 0, 1, 1) infinite alternate;
    }
    @keyframes blinker { to { opacity: 0; } }

	#first_block .map_link{
		margin: 107px 0 140px 0;
	}

	/* title style */
	#first_block .left_first_block > div:nth-child(2) > h1,
	#first_block .left_first_block > div:nth-child(2) > h2{
		font-family: 'Gilroy';
	}
	#first_block .left_first_block > div:nth-child(2) > h1:nth-child(1){
		font-size: 50px;
		color: white;
		font-weight: 300;
	}
	#first_block .left_first_block > div:nth-child(2) > h1:nth-child(1):before{
		content: "по всему Югу России";
		font-size: 16px;
		position: absolute;
		margin: -2rem 0 0 .2rem;
	}
    #first_block .best-price-guarantee > span{
        font-family: 'Gilroy';
        font-style: normal;
        font-weight: 300;
        font-size: 16px;
        line-height: 18px;
        color: #FFFFFF;
        transform: rotate(270deg);
        position: absolute;
        z-index: 10;
        left: -4rem;
        margin-top: 10rem;
    }
    #first_block .best-price-guarantee > svg{
        position: absolute;
    }
    @media (min-width: 1400px) and (max-width: 1460px) {
        #first_block .best-price-guarantee{
            display: none;
        }
    }
    @media (min-width: 1200px) and (max-width: 1280px) {
        #first_block .best-price-guarantee{
            display: none;
        }
    }
    @media (min-width: 990px) and (max-width: 1100px) {
        #first_block .best-price-guarantee{
            display: none;
        }
    }
    @media (min-width: 760px) and (max-width: 850px) {
        #first_block .best-price-guarantee{
            display: none;
        }
    }
    @media screen and (max-width: 650px) {
        #first_block .best-price-guarantee{
            display: none;
        }
    }

	#first_block .left_first_block > div:nth-child(2) > h1:nth-child(2){
		font-size: 50px;
		font-weight: 600;
	}
	#first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) > span:nth-child(2){
		font-family: 'Arial';
		font-size: 30px;
		color: white;
		font-weight: 900;
		position: absolute;
		z-index: 1010;
		margin: 12px 0 0 17px;

		background: white;
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
	}
    #first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) > span:nth-child(2) svg{
        width: 22px;
        height: 23px;
        margin-top: -7px;
    }
	#first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) > span:nth-child(1) {
		content: '';
		position: absolute;
		z-index: 1000;
		border-top: 37px solid #f17756;
		border-left: 12px solid transparent;
		margin-top: 12px;
		width: 150px;
	}
	#first_block .left_first_block > div:nth-child(2) > h2{
		font-size: 20px;
		color: white;
	}

	/* typing */
    #typing{
        margin: 35px 0 70px 0;
    }
	#typing > h2{
		padding-right: 0;
		font-size: 20px;
		color: white;
		font-family: 'Gilroy';
		text-transform: uppercase;
	}
	.typed-cursor{
		opacity: 1;
		animation: typedjsBlink 0.7s infinite;
		-webkit-animation: typedjsBlink 0.7s infinite;
		animation: typedjsBlink 0.7s infinite;

		width: fit-content;
		padding: 0;
		font-size: 20px;
		color: white;
		height: 32px;
		font-family: 'Gilroy';
		margin-top: 35px;
	}

	@keyframes typedjsBlink{
		50% { opacity: 0.0; }
	}

	@-webkit-keyframes typedjsBlink{
		0% { opacity: 1; }
		50% { opacity: 0.0; }
		100% { opacity: 1; }
	}

	.typed-fade-out{
		opacity: 0;
		transition: opacity .25s;
		-webkit-animation: 0;
		animation: 0;
	}

	/* advantages */
	#first_block .advantages{
		font-family: 'Arial';
	}
	#first_block .advantages p{
		font-size: 12px;
		line-height: 14px;
		color: rgba(125, 130, 142, 1);

		display: inline-block;
		position: absolute;
		margin: 4px 0 0 10px;
	}
	#first_block .advantages > div:nth-child(2) p{
		margin-left: 30px;
	}
	#first_block .advantages span{
        font-size: 42px;
        font-weight: 900;
        position: absolute;
        margin-top: -18px;
        font-family: 'ArialBlack';
        margin-left: -5px;
	}

	/* button */
	#first_block .btn-warning{
		font-family: 'Gilroy';
		font-size: 15px;
		text-transform: unset;
		background: var(--color_default);
		margin: 100px 0 140px 0;
		font-weight: 900;
		padding: .825rem 1.8rem;
	}
    #first_block .btn-warning:hover{
        box-shadow: 0px 0px 39px #F88253;
    }

	/* logo footer */
	#first_block .logo_footer{
		margin-left: -10px;
	}


	@media screen and (max-width: 1750px) {
		#first_block .logo_svg{
			margin: -.5rem 10rem 0 10rem;
		}
	}

	@media screen and (max-width: 1400px) {
        #typing{
            height: 60px;
        }

        #first_block .shining{
            top: -400px;
            left: -70px;
        }

        .navbar-expand-lg .navbar-nav .nav-item .nav-link{
            padding-right: 0.7rem;
            padding-left: 0.7rem;
        }

		#first_block .logo_svg{
			margin: -.5rem 6rem 0 6rem;
		}

		#first_block .right_first_block img{
			width: 100%;
		}
	}

	@media screen and (max-width: 1200px) {
        #first_block .shining{
            top: -340px;
            left: 15px;
        }

        .navbar-expand-lg .navbar-nav .nav-item .nav-link{
            padding-right: .6rem;
            padding-left: .6rem;
        }

		#first_block .logo_svg{
			margin: -.5rem 5rem 0 5rem;
		}

		/*#first_block nav .navbar-nav li:nth-child(6){
			margin-left: 16.5rem;
		}*/

		#first_block .left_first_block > div:nth-child(2) > h1:nth-child(1),
		#first_block .left_first_block > div:nth-child(2) > h1:nth-child(2){
			font-size: 40px;
		}

		#first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) > span:nth-child(1) {
			border-top: 29px solid #f17756;
			border-left: 11px solid transparent;
			margin-top: 10px;
			width: 130px;
		}
        #first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) > span:nth-child(2) svg {
            width: 18px;
            height: 19px;
            margin-top: -6px;
        }
		#first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) > span:nth-child(2) {
			font-size: 24px;
		}

		#first_block .left_first_block > div:nth-child(2) > h2,
		#typing,
		.typed-cursor{
			font-size: 16px;
		}

		#first_block .advantages p {
			font-size: 11px;
		}

		#first_block .btn-warning {
			font-size: 13px;
			padding: .825rem 1rem;
			width: 100%;
		}

		#first_block .right_first_block img{
			width: 100%;
		}
	}

	@media screen and (max-width: 992px) {
        #first_block .shining{
            display: none !important;
        }

        .navbar-expand-lg .navbar-nav .nav-item .nav-link{
            padding-left: 0px;
        }

        #first_block .btn-warning {
            margin: 4rem 0 0rem 0;
        }

		#first_block .logo_svg {
			display: none;
		}

		#first_block #logo_mobile .logo_svg{
			display: block;
			margin: 0 0 0 -.5rem;
		}

		/*#first_block nav .navbar-nav li:nth-child(6){
			margin-left: 0rem;
		}*/

		#first_block .left_first_block > div:nth-child(1) img{
			width: 30px;
		}
		#first_block .left_first_block > div:nth-child(1) > div:nth-child(1) > span {
			font-size: 10px;
		}
		#first_block .left_first_block > div:nth-child(1) > div:nth-child(2) > span,
		#first_block .map_link > span{
			font-size: 12px;
			margin-top: -.2rem;
		}

		#first_block .map_link {
            margin: 2em 0 3rem -8rem;
		}
	}

	@media screen and (max-width: 768px) {
        #first_block .shining{
            display: none !important;
        }

        .navbar-expand-lg .navbar-nav .nav-item .nav-link{
            padding-left: 0px;
        }

		#first_block .logo_svg {
			display: none;
		}

		#first_block .left_first_block > div:nth-child(1) img{
			width: 30px;
		}
		#first_block .left_first_block > div:nth-child(1) > div:nth-child(1) > span {
			font-size: 10px;
		}
		#first_block .left_first_block > div:nth-child(1) > div:nth-child(2) > span,
		#first_block .map_link > span{
			font-size: 8px;
		}

		#first_block .right_first_block img{
			display: none;
		}

		#first_block .btn-warning > svg{
			display: inline;
		}
		#first_block .map_link {
			margin: 4.5rem 0 6rem 2rem;
		}
        #first_block .map_link > span > a:after{
            margin-left: 0px;
        }
	}

	@media screen and (max-width: 576px) {
        #first_block .shining{
            display: none !important;
        }

        #typing > h2{
            font-weight: 400;
            font-size: calc(16px - 1px + (100vw - 320px) * 0.06);
        }
        #first_block .fbMargin {
            margin: calc(35px + (100vw - 320px) * 0.05) 0;
        }
        #typing {
            margin: calc(20px + (100vw - 320px) * 0.05) 0 calc(50px + (100vw - 320px) * 0.2) 0;
            height: 50px;
        }

        #first_block .btn-warning {
            margin: 40px auto 0rem auto;
        }

        .navbar-expand-lg .navbar-nav .nav-item .nav-link{
            padding-left: 0px;
        }

        #first_block #logo_mobile{
            position: absolute;
            left: 12px;
            top: 23px;
        }

        #first_block nav > div{
            flex-direction: row-reverse;
        }
        #first_block .left_first_block > div:nth-child(2) > h1:nth-child(1)::before {
            font-size: calc(10px - 1px + (100vw - 320px) * 0.04);
            margin-top: calc((20px + (100vw - 320px) * 0.1) * -1);
        }

		#first_block .logo_svg {
			display: none;
		}

		#first_block .left_first_block > div:nth-child(1) img{
			width: 30px;
		}
		#first_block .left_first_block > div:nth-child(1) > div:nth-child(1) > span {
			font-size: 10px;
		}
		#first_block .left_first_block > div:nth-child(1) > div:nth-child(2) > span,
		#first_block .map_link > span{
			font-size: 8px;
		}

		#first_block .left_first_block > div:nth-child(2) > h1:nth-child(1),
		#first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) {
            font-size: calc(25px - 1.7px + (100vw - 320px) * 0.1);
            font-weight: 400;
		}

		#first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) > span:nth-child(1) {
            border-top: calc(20px - 1px + (100vw - 320px) * 0.1) solid #f17756;
            border-left: calc(8px + (100vw - 320px) * 0.02) solid transparent;
            margin-top: 4px;
            width: calc(88px + (100vw - 320px) * 0.4);
            margin-left: calc(10px + (100vw - 320px) * 0.02);
		}

		#first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) > span:nth-child(2) {
            font-size: calc(10px + (100vw - 320px) * 0.1);
            margin: 4px 0 0 calc(35px + (100vw - 320px) * 0.04);
            line-height: calc(20px + (100vw - 320px) * 0.1);
            font-family: 'ArialBlack';
		}
        #first_block .advantages span{
            font-family: 'ArialBlack';
        }
        #first_block .advantages > div:nth-child(2) p {
            margin-left: calc(30px + (100vw - 320px) * 0.05);
        }

		#first_block .left_first_block > div:nth-child(2) > h2,
		#typing,
		.typed-cursor{
            font-size: calc(16px - 1px + (100vw - 320px) * 0.06);
		}
        .typed-cursor {
            margin-top: .5rem !important;
            margin-bottom: 1.5rem !important;
        }

        #first_block .advantages p {
            margin: 4px 0 0 calc(5px + (100vw - 320px) * 0.05);
            font-size: calc(10px + (100vw - 320px) * 0.02);
        }

        #first_block .btn-warning {
            margin: calc(40px + (100vw - 320px) * 0.2) auto 0rem auto;
            width: 85%;
            min-width: 70% !important;
            font-size: calc(14px + (100vw - 320px) * 0.03);
            padding: calc(10.5px + (100vw - 320px) * 0.03) 0rem;
        }

        #first_block .logo_footer{
            margin-left: 0px;
            width: calc(78px + (100vw - 320px) * 0.45);
        }

		#first_block .map_link {
            margin: calc(30px + (100vw - 320px) * 0.05) auto calc(50px + (100vw - 320px) * 0.1) auto;
		}
        #first_block .map_link > span > a{
            font-weight: 900;
            font-size: calc(14px + (100vw - 320px) * 0.01);
            line-height: 18px;
            text-decoration-line: underline;
        }
        #first_block .map_link > svg{
            margin-left: -10rem;
        }

        #first_block .map_link > span > a::after {
            margin-left: 27px;
        }

        #first_block .left_first_block > div:nth-child(1) > div:nth-child(2) > span, #first_block .map_link > span {
            margin-top: -1px;
        }

        #first_block .left_first_block > div:nth-child(2) > h1:nth-child(2) > span:nth-child(2) svg{
            width: calc(8px + (100vw - 320px) * 0.07);
            height: calc(9px + (100vw - 320px) * 0.07);
            margin-top: calc( (2.5px + (100vw - 320px) * 0.02) *-1);
        }

        #first_block #logo_mobile .logo_svg{
            width: calc(118px + (100vw - 320px) * 0.25);
        }

        #first_block .phone-header-mobile{
            font-size: calc(18px + (100vw - 320px) * 0.04);
            line-height: calc(21px + (100vw - 320px) * 0.04);
        }

        #first_block .navbar-toggler[aria-expanded="true"] + .phone-header-mobile{
            display: none !important;
        }
        #first_block .collapsed + .phone-header-mobile{
            display: block !important;
        }
    }
</style>

<link rel="preload" href="{{url('/data/style/css/plaster/spinner.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{url('/data/style/css/plaster/second_block.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
{{--<link rel="preload" href="{{url('/data/style/css/plaster/third_block.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">--}}
<link rel="preload" href="{{url('/data/style/css/plaster/calk.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{url('/data/style/css/plaster/third_block_next.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{url('/data/style/css/plaster/new_fourth_block.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{url('/data/style/css/plaster/bestPriceGuarantee.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{url('/data/style/css/plaster/fifth_block.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{url('/data/style/css/plaster/questions.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{url('/data/style/css/plaster/sixth_block.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{url('/data/style/css/plaster/seventh_block.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{url('/data/style/css/plaster/successCallbackModal.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
