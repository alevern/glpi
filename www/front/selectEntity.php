<?php


include ('../inc/includes.php');

Html::header2('', '');


// Change profile system
if (isset($_POST['newprofile'])) {
   if (isset($_SESSION["glpiprofiles"][$_POST['newprofile']])) {
      Session::changeProfile($_POST['newprofile']);

      //FEMTO-ST 2018-11-05 Tristan Cardot ## DEBUT ## Si l'utilisateur passe en post-only, redirige sur la selection du service, sinon redirige sur central.
      if ($_POST['newprofile'] == 1) {
         Html::redirect($CFG_GLPI['root_doc']."/front/selectEntity.php");
      } else if (Session::getCurrentInterface() == "helpdesk") {
         if ($_SESSION['glpiactiveprofile']['create_ticket_on_login']) {
            Html::redirect($CFG_GLPI['root_doc'] . "/front/helpdesk.public.php?create_ticket=1");
         } else {
            Html::redirect($CFG_GLPI['root_doc']."/front/helpdesk.public.php");
         }
      } else {
         Html::redirect($CFG_GLPI['root_doc']."/front/central.php");
      }
      //FEMTO-ST 2018-11-05 Tristan Cardot ## FIN ## Si l'utilisateur passe en post-only, redirige sur la selection du service, sinon redirige sur central.

      $_SESSION['_redirected_from_profile_selector'] = true;
      Html::redirect($_SERVER['HTTP_REFERER']);
   }
   Html::redirect(preg_replace("/entities_id.*/", "", $_SERVER['HTTP_REFERER']));
}

// Manage entity change
if (isset($_GET["active_entity"])) {
   $_GET["active_entity"] = rtrim($_GET["active_entity"], 'r');
   if (!isset($_GET["is_recursive"])) {
      $_GET["is_recursive"] = 0;
   }
   if (!Session::changeActiveEntities($_GET["active_entity"], $_GET["is_recursive"])) {
      if (($_GET["active_entity"] != $_SESSION["glpiactive_entity"])
          && isset($_SERVER['HTTP_REFERER'])) {
         Html::redirect(preg_replace("/(\?|&|".urlencode('?')."|".urlencode('&').")?(entities_id|active_entity).*/", "", $_SERVER['HTTP_REFERER']));
      }
   }
}

echo "<br><br>";


## FEMTO-ST 2018-09-11 Tristan Cardot ## DEBUT ## Modification de la page d'accueil des services commun.

$_SESSION["glpiactiveprofile"]["interface"] = "helpdesk";
$create = "";
if ($_SESSION['glpiactiveprofile']['create_ticket_on_login']) {
	$create = "create_ticket=1&";
}

echo '
<style>
		#c_menu {
			padding-left: 0;
		}
	
		.sc-container {
			position: relative;
			width: 75vw;
			margin: 20px auto 50px auto;
			display: grid;
			grid-template-columns: 1fr 1fr;
			grid-gap: 1vw;
		}
	
		.sc-title {
			text-align: center;
			font-size: 2.4em;
			font-weight: bold;
			margin-top: 50px;
		}
	
		.sc-card {
			position: relative;
			padding-top: 14vh;
			display: inline-block;
			text-decoration: none;
			background-color: #0288D1;
			cursor: pointer;
			min-height: 60px;
			min-width: 45%;
			overflow: hidden;
		}
		.sc-card:hover {
			box-shadow: inset 0 0 0 0.5vw #4FC3F7;
		}
		.sc-card:active {
			background: #039BE5;
		}
	
		.sc-card.disabled {
			cursor: default;
			background: #BDBDBD;
		}
		.sc-card.disabled:hover {
			box-shadow: none;
		}
		.sc-card.disabled:active {
			background: #BDBDBD;
		}

			.sc-card-name {
				color: white;
				position: absolute;
				top: 15%;
				left: 5%;
				font-size: 3vw;
				width: 75%;
				line-height: 1em;
			}
			.sc-card-name::first-letter {
				font-size: 4vw;
			}

			.sc-card-icon {
				position: absolute;
				bottom: 0;
				right: 0;
				height: 80%;
				fill: none;
				opacity: 0.22;
				stroke: rgba(255, 255, 255);
				stroke-width: 2;
			}
	</style>
	<h1 class="sc-title">'.__('Selectionnez votre service commun FEMTO-ST').'</h1>
	<div class="sc-container">
		<a href="/front/helpdesk.public.php?'.$create.'active_entity=1#modal_entity_content" class="sc-card">
			<span class="sc-card-name">'.__('Informatique').'</span>
			<svg class="sc-card-icon" viewBox="0 0 20.5 20.5" stroke-linecap="round">
				<rect x="2" y="2" width="20" height="8" rx="2" ry="2"/>
				<rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
				<line x1="6" y1="6" x2="6" y2="6"/>
				<line x1="6" y1="18" x2="6" y2="18"/>
			</svg>
		</a>
		<a href="/front/helpdesk.public.php?'.$create.'active_entity=2#modal_entity_content" class="sc-card disabled">
			<span class="sc-card-name">'.__('Mécanique').'</span>
			<svg class="sc-card-icon" viewBox="0 0 18 18">
				<circle cx="12" cy="12" r="3"/>
				<path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
			</svg>
		</a>
		<a href="/front/helpdesk.public.php?'.$create.'active_entity=4#modal_entity_content" class="sc-card disabled">
			<span class="sc-card-name">'.__('Communication').'</span>
			<svg class="sc-card-icon" viewBox="0 2 30 30">
				<path stroke-linecap="round" d="M20 20 l7 0 a 3 3 0 0 1 3 3 l0 4 a 3 3 0 0 1 -3 3 l-4 0 l-3 3 l-3 -3 l-4 0 a 3 3 0 0 1 -3 -3 l0 -4 a 3 3 0 0 1 3 -3 Z m-6 5 l12 0"/>
				<path d="M22 18 l0 -5 l-20 0 l0 13 l3 -3 l3 0 m-3 -6.5 l14 0 m-14 3 l3.5,0 0.2,0.1 0.2,0.25"/>
				<path	d="m 14.15,10.91 c -0.01,-0.66 1.75,-4.83 9.87,-4.20 
						6.03,0.47 7.83,4.60 5.70,7.37 -0.15,0.20 
						0.93,2.36 0.73,2.55 -0.19,0.18 -1.67,-1.61 
						-1.90,-1.45 -1.07,0.75 -2.57,1.28 -4.48,1.40"/>
			</svg>
		</a>
		<a href="/plugins/formcreator/front/formdisplay.php?id=1&active_entity=9#modal_entity_content" class="sc-card ">
			<span class="sc-card-name">'.__('Électronique et Instrumentation').'</span>
			<svg class="sc-card-icon"  viewBox="8 8 35 35">
					<circle cx="24" cy="12" r="3"/>
					<circle cx="24" cy="24" r="3"/>
					<circle cx="24" cy="36" r="3"/>
					<circle cx="36" cy="12" r="3"/>
					<circle cx="36" cy="36" r="3"/>
					<path d="M60 12 L39 12 
						M12 60 L12 36 L21.5 26.5 M12 36 L12 24 L21.5 14.5
						M60 24 L48 24 L38.5 33.5 M48 24 L36 24 L26.5 33.5 M24 39 L24 60"></path>
			</svg>
		</a>
		<a href="/front/helpdesk.public.php?'.$create.'active_entity=5#modal_entity_content" class="sc-card disabled">
			<span class="sc-card-name">'.__('Qualité').'</span>
			<svg class="sc-card-icon" viewBox="-6 3 25 25" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="12" cy="12" r="8"/>
				<path d="M8 12.5 l3 3 l5 -5 
							M17 18.5 l3,8 -3,-1 -2.5,1.7 -1,-7
							M7 18.5 l-3,8 3,-1 2.5,1.7 1,-7"/>
			</svg>
		</a>
		<a href="/front/helpdesk.public.php?'.$create.'active_entity=6#modal_entity_content" class="sc-card disabled">
			<span class="sc-card-name">'.__('Infrastructures').'</span>
			<svg class="sc-card-icon" viewBox="-1 -1 18.5 18.5" stroke-linejoin="round">
				<path d="M0,20 l0,-15 10,0 0,15 0,-20 10,0
					M2.3,8.3 l2,0 m1.3,0 l2,0
					m-5.3,3.3 l2,0 m1.3,0 l2,0
					m-5.3,3.3 l2,0 m1.3,0 l2,0
					M12.3,3.3 l2,0 m1.3,0 l2,0
					m-5.3,3.3 l2,0 m1.3,0 l2,0
					m-5.3,3.3 l2,0 m1.3,0 l2,0
					m-5.3,3.3 l2,0 m1.3,0 l2,0"/>
			</svg>
		</a>
		<a href="/front/helpdesk.public.php?'.$create.'active_entity=7#modal_entity_content" class="sc-card disabled">
			<span class="sc-card-name">'.__('Gestion').'</span>
			<svg class="sc-card-icon" viewBox="0 0 90 90" stroke-linecap="round" stroke-linejoin="round">
				<path style="fill:white;" d="
					M50,50
					L91.63673686238113 28.196740072804882 A 47 47 0 1 1 86.41565009534001 20.286359561074256
					L68.09206674809808,31.54963271559285
					L77.18481997603845 16.68355416809319 A 43 43 0 1 0 91.32078020104092 38.10070911452011
					L50,50"/>
			</svg>
		</a>
		<a href="/front/helpdesk.public.php?'.$create.'active_entity=8#modal_entity_content" class="sc-card disabled">
			<span class="sc-card-name">'.__('Mimento Facilities').'</span>
			<svg class="sc-card-icon" viewBox="0 0 100 100" stroke-linecap="round" stroke-linejoin="round"
					style="transform: translate(10%, 10%) rotate(35deg)">
				<circle cx="50" cy="50" r="47.5" style="stroke-width:5"/>
				<path d="M16,16L84,16L84,84L16,84Z
						M33,6L33,94M50,2L50,98M67,6L67,94
						M6,33L94,33M2,50L98,50M6,67L94,67"/>
			</svg>
		</a>
		<a href="/front/helpdesk.public.php?'.$create.'active_entity=10#modal_entity_content" class="sc-card disabled">
			<span class="sc-card-name">'.__('CMNR').'</span>
			<svg class="sc-card-icon" viewBox="0 0 100 100" stroke-linecap="round" stroke-linejoin="round"
					style="transform: translate(10%, 10%) rotate(35deg)">
				<circle cx="50" cy="50" r="47.5" style="stroke-width:5"/>
				<path d="M16,16L84,16L84,84L16,84Z
						M33,6L33,94M50,2L50,98M67,6L67,94
						M6,33L94,33M2,50L98,50M6,67L94,67"/>
			</svg>
		</a>
		<a href="/front/helpdesk.public.php?'.$create.'active_entity=11#modal_entity_content" class="sc-card disabled">
			<span class="sc-card-name">'.__('Publiweb ENERGIE').'</span>
			<svg class="sc-card-icon" viewBox="0 0 100 100" stroke-linecap="round" stroke-linejoin="round"
					style="transform: translate(10%, 10%) rotate(35deg)">
				<circle cx="50" cy="50" r="47.5" style="stroke-width:5"/>
				<path d="M16,16L84,16L84,84L16,84Z
						M33,6L33,94M50,2L50,98M67,6L67,94
						M6,33L94,33M2,50L98,50M6,67L94,67"/>
			</svg>
		</a>
	</div>
';

## FEMTO-ST 2018-09-11 Tristan Cardot ## FIN ## Modification de la page d'accueil des services commun.

Html::footer();
?>
