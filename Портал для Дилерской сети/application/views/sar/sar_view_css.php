<style>
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}
	input[type='number'] {
		-moz-appearance: textfield;
	}


	.title_sar{
		background: #223464;
		color: white;
		padding-left: 20px;
		margin: 0px;
	}
	.title_sar:not(.title_sar:first-child){
		margin-top: 30px;
	}


	#SAR_{
		padding: 0px 15px;
	}

		#SAR_ table.SAR_testdrive td, #SAR_ table.SAR_offer td{
			cursor: pointer;
		}

	.help_icon_btn{
		width: 35px !important;
		height: 35px !important;
		margin-left: 25px !important;
	}

	#SAR_ table{
		border-collapse: inherit !important;
	}
	#SAR_ table tbody > tr:nth-of-type(2n + 1) {
		background-color: #ffffff !important;
	}
	#SAR_ table tbody tr:nth-of-type(2n) {
		background: #ffffff !important;
	}
	#SAR_ table.sar_traffic{
		width: 100%;
		overflow-x: auto;
		font-size: 14px;
	}
	#SAR_ table.sar_traffic input{
		width: 100%;
		height: 100%;
		border: none;
		text-align: center;
		min-width: 20px;
	}
	#SAR_ table.sar_traffic th, #SAR_ table.sar_traffic td{
		background: white;
		border: 1px solid #223464;
		/* padding: 2px 5px; */
		text-align: center;
		min-width: 30px;
	}
	#SAR_ table.sar_traffic th:first-child, #SAR_ table.sar_traffic td:first-child{
		text-align: left;
	}
	#SAR_ table.sar_traffic th:last-child, #SAR_ table.sar_traffic td:last-child{
		font-weight: bold;
		background: #d5d5d5;
	}
	#SAR_ table.sar_traffic th{
		color: #223464;
	}
	#SAR_ table.sar_traffic tr:last-child td{
		font-weight: bold;
		background: #d5d5d5;
	}


	.content{
		min-height: 0px;
	}


	.sar_total{
		margin: 30px 0px;
		font-size: 14px;
	}
	.sar_total td{
		padding: 2px 5px;
		border: 1px solid black;
		color: white;
	}
	.sar_total td:nth-child(2){
		color: black;
		font-weight: bold;
	}
	.sar_total tr:first-child td:first-child{
		background: #b2b2b2;
	}
	.sar_total tr:nth-child(2) td:first-child{
		background: #f00;
	}
	.sar_total tr td:not(.sar_total tr:nth-child(2) td:first-child, .sar_total tr:nth-child(3) td:first-child, .sar_total tr:nth-child(4) td:first-child, .sar_total tr:nth-child(1) td:nth-child(2), .sar_total tr:nth-child(2) td:nth-child(2), .sar_total tr:nth-child(3) td:nth-child(2), .sar_total tr:nth-child(4) td:nth-child(2), .sar_total tr:last-child td:nth-child(2)){
		background: #223464;
	}
	.sar_total tr:nth-child(3) td:first-child{
		background: rgb(26 35 126);
	}
	.sar_total tr:nth-child(4) td:first-child{
		background: rgb(0 112 161);
	}


	.sar_analysis{
		font-size: 14px;
		margin-top: 20px;
		text-align: center;
	}
	.sar_analysis th{
		padding: 5px 10px;
		background: #223464;
		color: white;
		border: 1px solid black;
	}
	.sar_analysis td{
		padding: 2px 5px;
		background: white;
		border: 1px solid black;
	}
	#SAR_ > div{
		width: 45%;
		display: inline-block;
		margin-top: 15px;
		margin-bottom: 15px;
		margin-right: 5%;
		position: relative;
	}
	#SAR_ > div > canvas{
		max-width: 500px;
		height: 250px;
		margin: auto;
		/*opacity: 0.5;*/
	}
	.chart_preloader {
		position: absolute;
		left: 50%;
		top: 50%;
		display: none;
	}
	
	.chart_preloader:not(:required):after {
	  content: '';
	  display: block;
	  font-size: 10px;
	  width: 1em;
	  height: 1em;
	  margin-top: -0.5em;
	  -webkit-animation: spinner 1500ms infinite linear;
	  -moz-animation: spinner 1500ms infinite linear;
	  -ms-animation: spinner 1500ms infinite linear;
	  -o-animation: spinner 1500ms infinite linear;
	  animation: spinner 1500ms infinite linear;
	  border-radius: 0.5em;
	  -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
	  box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
	}

	@keyframes glowing {
		0% { background-color: #f21212; box-shadow: 0 0 5px #f21212; }
		50% { background-color: #f21212; box-shadow: 0 0 20px #f21212; }
		100% { background-color: #f21212; box-shadow: 0 0 5px #f21212; }
	}
	.btn_blink {
		animation: glowing 1300ms infinite;
	}

	.loading_SAR{
		display: none;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 1000;
		background: white;
	}
	.loading_SAR img{
		height: 250px;
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		margin: auto;
	}


	@media screen and (max-width: 992px){
		#SAR_ > div{
			width: 100%;
			display: block;
		}
	}


/*
	@author aws
	@version 2021-08-03'
*/

#version {
    position: absolute;
    top: 80px;
    right: 0px;
    height: 20px;
    width: auto;
    color: red;
    font-size: 14px;
    padding-right: 15px;
}

#myModal {
	font-size:  0.9em;
}

#contracts_table {
	font-size:  1em;
}

.modal-xl {
	width:  1300px;
}

@media (max-width: 1400px) {
  .modal-xl {
    max-width: 95%;
  }
}

</style>

