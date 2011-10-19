-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 19 Octobre 2011 à 19:36
-- Version du serveur: 5.1.41
-- Version de PHP: 5.3.2-1ubuntu4.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `islamine`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) NOT NULL,
  `content` longtext NOT NULL,
  `id_category` int(11) NOT NULL,
  `date_posted` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `article`
--

INSERT INTO `article` (`id`, `title`, `content`, `id_category`, `date_posted`) VALUES
(1, 'Coran : livre violent ?', 'Dans la société actuelle nous pouvons souvent lire ou entendre des arguments contre le Coran. L''argument principal des détracteurs du Coran porte sur la soi-disant violence qu''enseigne le Coran, qui entraine la violence aujourd''hui provoquée par l''islam.\r\nCependant il ne faut pas oublier de replacer les versets dans le contexte historique dans lequel ils ont été révélés. Certains sont une réponse à des évènements particuliers survenus à l’époque.\r\n\r\nIl faut savoir qu’à cette époque les musulmans étaient attaqués et persécutés pour avoir laissé les croyances de leurs ancêtres. Ces versets autorisent les musulmans de se défendre dans des conditions bien précises.\r\n\r\nPar exemple le verset 191 de la Sourate 2 : <span class="sourate">« Et tuez-les, où que vous les rencontriez ; et chassez-les d’où ils vous ont chassés : l’association est plus grave que le meurtre. »</span>\r\n\r\nSi l’ont regarde le verset précédent et les deux suivants, on voit qu’il n’est pas autorisé de faire violence injustement. \r\n\r\n<span class="sourate">« 190. Combattez dans le sentier d’Allah ceux qui vous combattent, et ne transgressez pas. Certes, Allah n’aime pas les transgresseurs.\r\n191. Et tuez-les, où que vous les rencontriez ; et chassez-les d’où ils vous ont chassés : l’association est plus grave que le meurtre. Mais ne les combattez pas près de la Mosquée sacrée avant qu’ils ne vous y aient combattus. S’ils vous y combattent, tuez-les donc. Telle est la rétribution des mécréants.\r\n192. S’ils cessent, Allah est, certes, Pardonneur et Miséricordieux.\r\n193. Et combattez-les jusqu’à ce qu’il n’y ait plus de l’association, et que la religion soit entièrement à Allah seul. S’ils cessent, donc plus d’hostilités, sauf contre les injustes. »</span>\r\n\r\nCeci est valable dans un contexte de guerre déjà engagée. Il est seulement permis de se défendre contre les assaillants, sinon que resterait-il de l’islam s’ils avaient laissé tout le monde se faire tuer ?\r\nSi les incroyants arrêtent les hostilités alors il n’y a pas de raison de continuer à se battre. La guerre n’est pas faite par plaisir.\r\n\r\n\r\n<span class="sourate">Sourate IX, 29: Combattez ceux qui ne croient ni en Allah ni au Jour dernier, qui n’interdisent pas ce qu’Allah et Son messager ont interdit et qui ne professent pas la religion de la vérité, parmi ceux qui ont reçu le Livre, jusqu’à ce qu’ils versent la capitation par leurs propres mains, après s’être humiliés.</span>\r\n\r\nLe verset précédent dit :\r\n<span class="sourate">28. Ô vous qui croyez ! Les associateurs ne sont qu''impureté : qu''ils ne  s''approchent plus de la Mosquée sacrée, après cette année-ci. Et si vous  redoutez une pénurie, Allah vous enrichira, s''Il veut, de par Sa grâce. Car Allah est Omniscient et Sage.</span>\r\n\r\nCes versets indiquent qu’après « cette année-ci » (année 8 après l’Hégire), les polythéistes de pourraient plus se servir de la Mosquée sacrée pour leur culte idolâtre. Le verset 29 suit la logique du verset 28, et parle donc ce des polythéistes pour les empêcher d’utiliser la Moquée sacrée.\r\n\r\n\r\n<span class="sourate">Sourate IV, 89. Ils aimeraient vous voir mécréants, comme ils ont mécru : alors vous seriez tous égaux ! Ne prenez donc pas d''alliés parmi eux, jusqu''à ce qu''ils émigrent dans le sentier d''Allah. Mais s''ils tournent le dos, saisissez-les alors, et tuez-les où que vous les trouviez ; et ne prenez parmi eux ni allié ni secoureur.</span>\r\n\r\nVersets 88 à 91\r\n<span class="sourate">88. Qu''avez-vous à vous diviser en deux factions au sujet des hypocrites ? Alors qu’Allah les a refoulés (dans leur infidélité) pour ce qu''ils ont acquis. Voulez- vous guider ceux qu’Allah égare ? Et quiconque Allah égare, tu ne lui trouveras pas de chemin (pour le ramener).\r\n89. Ils aimeraient vous voir mécréants, comme ils ont mécru : alors vous seriez tous égaux ! Ne prenez donc pas d''alliés parmi eux, jusqu''à ce qu''ils émigrent dans le sentier de Dieu. Mais s''ils tournent le dos, saisissez-les alors, et tuez-les où que vous les trouviez; et ne prenez parmi eux ni allié ni secoureur, \r\n90. excepté ceux qui se joignent à un groupe avec lequel vous avez conclu une alliance, ou ceux qui viennent chez vous, le cœur serré d''avoir à vous combattre ou à combattre leur propre tribu. Si Allah avait voulu, Il leur aurait donné l''audace (et la force) contre vous, et ils vous auraient certainement combattu. (Par conséquent,) s''ils restent neutres à votre égard et ne vous combattent point, et qu''ils vous offrent la paix, alors, Allah ne vous donne pas de chemin contre eux. \r\n91 Vous en trouverez d''autres qui cherchent à avoir votre confiance, et en même temps la confiance de leur propre tribu. Toutes les fois qu''on les pousse vers l''Association, (l''idolâtrie) ils y retombent en masse. (Par conséquent,) s''ils ne restent pas neutres à votre égard, ne vous offrent pas la paix et ne retiennent pas leurs mains (de vous combattre), alors, saisissez-les et tuez les où que vous les trouviez. Contre ceux-ci, Nous vous avons donné autorité manifeste.</span>\r\n\r\nCes versets ont été révélés lorsque les musulmans étaient victimes d’attaques incessantes des non-musulmans de La Mecque. On peut voir qu’encore une fois, les musulmans sont appelés à se défendre, mais si les attaquants cessent il n’y a pas de raison de continuer le combat.\r\n\r\nCes versets n’autorisent absolument pas le meurtre comme peuvent le penser certaines personnes, il faut savoir que le meurtre est interdit en islam sauf dans un contexte précis cité plus haut.\r\n\r\n<span class="sourate">Le Coran [5:32]\r\nC''est pourquoi Nous avons prescrit pour les Enfants d''Israël que quiconque tuerait une personne non coupable d''un meurtre ou d''une corruption sur la terre, c''est comme s''il avait tué tous les hommes. Et quiconque lui fait don de la vie, c''est comme s''il faisait don de la vie à tous les hommes. En effet Nos messagers sont venus à eux avec les preuves. Et puis voilà, qu''en dépit de cela, beaucoup d''entre eux se mettent à commettre des excès sur la terre</span>\r\n\r\n<span class="sourate">Le Coran [2:84]\r\nEt rappelez-vous, lorsque Nous obtînmes de vous l''engagement de ne pas vous verser le sang, [par le meurtre] de ne pas vous expulser les uns les autres de vos maisons. Puis vous y avez souscrit avec votre propre témoignage.\r\n\r\n« C’est pourquoi Nous avons prescrit pour les Enfants d’Israël que quiconque tuerait une personne non coupable d''un meurtre ou d''une corruption sur la terre, c'' est comme s'' il avait tué tous les hommes. Et quiconque lui fait don de la vie, c''est comme s’il faisait don de la vie à tous les hommes » ( Coran, Sourate 5, Al Maïda, la table servie, verset 32 )</span>\r\n\r\nAu contraire, l’islam appelle à l’amour, au respect et à la paix.\r\nVous pourrez le constater en allant voir cet article : Amour, respect et paix.', 2, 0),
(2, 'Amour, respect et paix', 'Le Coran enseigne l’amour et la paix. Allah est Amour, Pardonneur et Miséricordieux. Il nous incite à l’être aussi.\r\n\r\nVersets du Coran :\r\n« Et que la paix soit sur moi le jour où je naquis, le jour où je mourrai, et le jour où je serai ressuscité vivant. » \r\n(Coran, Sourate 19, Myriam, Marie, verset 33)\r\n\r\n « Certes, Allah commande l''équité, la bienfaisance et l''assistance aux proches. Et Il interdit la turpitude, l''acte répréhensible et la rébellion. Il vous exhorte afin que vous vous souveniez. » (Coran, Sourate 16, An Nahl, les abeilles, verset 90)\r\n\r\n« Ô hommes ! Nous vous avons crées d’un mâle et d’une femelle, et Nous vous avons fait de vous des nations et des tribus, pour que vous vous entre-connaissiez ! Le plus noble d’entre vous, auprès d’Allah, est le plus pieux. Allah est certes Omniscient et Grand Connaisseur. » (Coran, Sourate 49, Al Houjourat, les appartements, verset 13)\r\n\r\nÔ les croyants! Soyez stricts (dans vos devoirs) envers Allah et (soyez) des témoins équitables. Et que la haine pour un peuple ne vous incite pas à être injuste. Pratiquez l''équité: cela est plus proche de la piété. Et craignez Allah. Car Allah est certes Parfaitement Connaisseur de ce que vous faites. (Le Coran, sourate al-Ma''ida, verset 8)\r\n\r\nLes musulmans doivent faire le bien et éviter le mal et doivent faire ce qu’ils peuvent pour être pieux.\r\nAllah aime la justice et l’équité, c’est pourquoi nous devons être le plus juste possible envers les autres et nous-mêmes.\r\nLe Coran nous invite aussi à apprendre à nous connaître, à découvrir différentes culture et à comprendre leur fonctionnement. Cela permet d’ouvrir son esprit, d’apprendre à aimer les autres et de découvrir la diversité du monde qu’Allah nous offre.\r\n\r\nCertaines paroles du Prophète surenchérissent cette vision :\r\n\r\n« Celui qui tue un citoyen non musulman, ne sentira jamais l’odeur du paradis »\r\n\r\n« Celui qui opprime un citoyen non musulman, qui lui retire ses droits, exige de lui plus qu’il ne peut supporter, et qui le contraint à une quelconque concession, je serai le défenseur de cet opprimé le jour du jugement dernier »  \r\n\r\nAbdullah Ibn Salam (un savant juif qui s’est converti à l’islam et qui devint un compagnon), après avoir entendu que le Prophète était arrivé à Médine rapporte les premières paroles qu’il a entendu :\r\n« Ô gens ! Transmettez entre vous le salut de paix, donnez à manger et priez la nuit lorsque les gens dorment pour que vous entriez au paradis dans la Paix. »\r\n\r\nLe musulman se doit d’être quelqu’un de respectueux envers les autres. \r\nL’islam est une religion d’amour qui incite à la paix, cependant l’Homme ne va pas toujours en ce sens, c’est pourquoi un bon sens de la justice est aussi apprécié.', 3, 0),
(3, 'Jésus est Dieu ?', 'Le Coran affirme plusieurs fois qu’Allah est unique et qu’il n’a pas de fils. Sans prétendre connaître ce qu’Allah connaît, si l’on réfléchit, il n’est pas logique que Celui qui a tout créé et qui n’a besoin de rien ni personne se donne un enfant.\r\nLe Coran nous l’indique à différentes reprises :\r\n\r\nSourate 2 verset 116 :\r\nEt ils ont dit : "Allah s''est donné un fils» ! Gloire à Lui ! Non ! Mais c''est à Lui qu''appartient ce qui est dans les cieux et la terre et c''est à Lui que tous obéissent. \r\n\r\nSourate 19 (Marie) verset 35 :\r\nIl ne convient pas à Allah de S''attribuer un fils. Gloire et Pureté à Lui ! Quand Il décide d''une chose, Il dit seulement : «Sois ! » et elle est.\r\n\r\nSourate 19 versets 88 à 95\r\n88. Et ils ont dit : «Le Tout Miséricordieux S''est attribué un enfant ! » \r\n89. Vous avancez certes là une chose abominable ! \r\n90. Peu s''en faut que les cieux ne s''entrouvrent à ces mots, que la terre ne se fende et que les montagnes ne s''écroulent, \r\n91. du fait qu''ils ont attribué un enfant au Tout Miséricordieux, \r\n92. alors qu''il ne convient nullement au Tout Miséricordieux d''avoir un enfant ! \r\n93. Tous ceux qui sont dans les cieux et sur la terre se rendront auprès du Tout Miséricordieux, [sans exceptions], en serviteurs. \r\n94. Il les a certes dénombrés et bien comptés. \r\n95. Et au Jour de la Résurrection, chacun d''eux se rendra seul auprès de Lui\r\n\r\n30. Les Juifs disent : «Uzayr est fils de Dieu» et les Chrétiens disent : «Le Christ est fils de Dieu». Telle est leur parole provenant de leurs bouches. Ils imitent le dire des mécréants avant eux. que Dieu les anéantisse ! Comment s''écartent-ils (de la vérité) ? \r\n31. Ils ont pris leurs rabbins et leurs moines, ainsi que le Christ fils de Marie, comme Seigneurs en dehors de Dieu, alors qu''on ne leur a commandé que d''adorer un Dieu unique. Pas de divinité à part Lui ! Gloire à Lui ! Il est au-dessus de ce qu''ils [Lui] associent. \r\n32. Ils veulent éteindre avec leurs bouches la lumière de Dieu, alors que Dieu ne veut que parachever Sa lumière, quelque répulsion qu''en aient les mécréants. \r\n33. C''est Lui qui a envoyé Son messager avec la bonne direction et la religion de la vérité, afin qu''elle triomphe sur toute autre religion, quelque répulsion qu''en aient les associateurs. \r\n34. Ô vous qui croyez ! Beaucoup de rabbins et de moines dévorent, les biens des gens illégalement et [leur] obstruent le sentier de Dieu. A ceux qui thésaurisent l''or et l''argent et ne les dépensent pas dans le sentier de Dieu, annonce un châtiment douloureux, \r\n35. le jour où (ces trésors) seront portés à l''incandescence dans le feu de l''Enfer et qu''ils en seront cautérisés, front, flancs et dos : voici ce que vous avez thésaurisé pour vous-mêmes. Goûtez de ce que vous thésaurisiez.» Sourat LE REPENTIR \r\n\r\n\r\nCes versets disent clairement que tout enfant attribué à Allah, n’est que mensonge.\r\n\r\nLes versets suivants réprouvent l’affirmation qui dit que Jésus est Dieu.\r\nCertes sont mécréants ceux qui disent : «Dieu, c''est le Messie, fils de Marie ! » - Dis : «Qui donc détient quelque chose de Dieu (pour L''empêcher), s''Il voulait faire périr le Messie, fils de Marie, ainsi que sa mère et tous ceux qui sont sur la terre ? ... A Dieu seul appartient la royauté des cieux et de la terre et de ce qui se trouve entre les deux». Il crée ce qu''Il veut. Et Dieu est Omnipotent. Sourat almaida , Verset 17.\r\n\r\nCe sont, certes, des mécréants ceux qui disent : «En vérité, Dieu c''est le Messie, fils de Marie.» Alors que le Messie a dit : «Ô enfants d''Israël, adorez Dieu, mon Seigneur et votre Seigneur». Quiconque associe à Dieu (d''autres divinités) Dieu lui interdit le Paradis; et son refuge sera le Feu. Et pour les injustes, pas de secoureurs ! Sourat almaida , Verset 72 \r\n\r\nLe Messie, fils de Marie, n''était qu''un Messager. Des messagers sont passés avant lui. Et sa mère était une véridique. Et tous deux consommaient de la nourriture. Vois comme Nous leur expliquons les preuves et puis vois comme ils se détournent. \r\n76. Dis : «Adorez-vous, au lieu de Dieu, ce qui n''a le pouvoir de vous faire ni le mal ni le bien ? » Or c''est Dieu qui est l''Audient et l''Omniscient. Sourat almaida,Verset 75 \r\n79. Il ne conviendrait pas à un être humain à qui Dieu a donné le Livre, la Compréhension et la Prophétie, de dire ensuite aux gens : «Soyez mes adorateurs, à l''exclusion de Dieu»; mais au contraire, [il devra dire]: «Devenez des savants, obéissant au Seigneur, puisque vous enseignez le Livre et vous l''étudiez». \r\n80. Et il ne va pas vous recommander de prendre pour seigneurs anges et prophètes. Vous commanderait-il de rejeter la foi, vous qui êtes Musulmans ? sourat alimran Verset 79\r\n\r\nCela signifie donc que l’essence du message de la Bible a été modifiée. Pourquoi avoir discuté et modifié certaines choses ? Un élément de réponse peut être apporté, avec les décisions qui ont été prises au concile de Nicée en 325 après Jésus-Christ. Ce concile, convoqué par l’empereur Constantin, réunissait les personnes les plus importantes de l’Eglise.\r\nLe vote avait pour but de décider si le Fils était de la même nature et substance que le Père. Il en est ressorti que Jésus Fils de Dieu est de même substance que le Père.\r\nLa confession de foi suivante a été adoptée :\r\n\r\n« Nous croyons en un seul Dieu, Père tout-puissant, Créateur de toutes choses visibles et invisibles ; et en un seul Seigneur Jésus-Christ, Fils unique de Dieu, engendré du Père, c''est-à-dire, de la substance du Père. Dieu de Dieu, lumière de lumière, vrai Dieu de vrai Dieu ; engendré et non fait, consubstantiel au Père ; par qui toutes choses ont été faites au ciel et en la terre. Qui, pour nous autres hommes et pour notre salut, est descendu des cieux, s''est incarné et s''est fait homme ; a souffert, est ressuscité le troisième jour, est monté aux cieux, et viendra juger les vivants et les morts. Nous croyons aussi au Saint-Esprit. » (wikipédia)\r\n\r\nOn peut penser que l’Eglise a modifié le message original par abus de pouvoir mais Allah sait mieux.\r\n\r\nPour terminer, dans la Bible, Jésus dit lui-même que Dieu est unique :\r\nEvangile selon Saint Marc\r\nChapitre 12\r\n28. Et s''approcha un des scribes, qui avait entendu leur discussion; voyant qu''il leur avait bien répondu, il lui demanda : " Quel est le premier de tous les commandements? "\r\n29. Jésus répondit : " Le premier, c''est : Ecoute Israël : le Seigneur notre Dieu, le Seigneur est un.\r\n30. Et tu aimeras donc le Seigneur ton Dieu, de tout ton cœur, de toute ton âme, de tout ton esprit, et de toute ta force.\r\n31. Le second est celui-ci : Tu aimeras ton proche comme toi-même. Il n''y a pas d''autre commandement plus grand que ceux-là. "\r\n32. Le scribe lui dit : " Bien, Maître, vous avez dit avec vérité qu''Il est un et qu''il n''en est point d''autre que lui;\r\n33. et que l''aimer de tout son cœur, de toute son intelligence et de toute sa force, et aimer le proche comme soi-même, c''est plus que tous les holocaustes et sacrifices. "\r\n\r\n\r\nQu’Allah me pardonne si j’ai commis une quelconque erreur.\r\n Gloire et Louange à Allah', 4, 0),
(4, 'L''image de l''islam en occident', 'L’islam en occident a été décrédibilisé, surtout ces dernières années. Nous pouvons voir à travers les informations qui baignent notre quotidien des images, des reportages, des interviews qui donnent une mauvaise image de l’islam, mais c’est surtout, ce qui est montré n’est pas l’islam. Malheureusement peu de gens sont informés et par conséquent la plupart prennent pour vrai ce qu’ils voient ou entendent. Sans entrer dans des débats, cette désinformation est volontaire de la part des gouvernements pour faire peur aux populations.\r\nLe véritable islam est bafoué par le flot d’information qui se focalise uniquement sur des mouvements extrémistes (dont certains ont d’ailleurs été fondés par des européens) qui ne sont même pas considéré comme des musulmans par la plupart des musulmans eux-mêmes.\r\nLes médias tentent de généraliser cette image de l’islam à travers les esprits et y parviennent.\r\nSeulement l’islam ce n’est pas le terrorisme, ce n’est pas la violence engendrée par ce qu’on peut voir aux informations, le véritable islam prône la paix.\r\nIl ne faut pas non plus confondre l’islam avec les musulmans.\r\nL’islam c’est une religion avec un Livre, un Dieu et un Prophète (ainsi que les autres prophètes avant) qui enseigne aux Hommes les lois de la vie. Les musulmans sont les adeptes de cette religion, mais ce sont avant tout des êtres humains qui peuvent se dire musulman, alors qu’en réalité ils ne sont pas réellement dans l’islam. L’islam c’est une façon de voir les choses et de vivre, il ne suffit pas de dire « Je suis musulman » pour l’être. Tout comme les personnes durant les croisades qui se disaient chrétiennes certaines aujourd’hui se disent musulmanes, et les médias en profitent pour faire de la propagande anti-islamique.\r\nPourquoi entendons-nous parler des pays du Moyen-Orient comme des terroristes islamiques alors qu’on entend jamais parler de l’Indonésie qui est le pays le plus musulman du monde ? Peut-être parce qu’il n’y a pas de raison pour les gouvernements occidentaux d’aller dans ce pays… à méditer.\r\n\r\nPour mieux comprendre l’islam il faut se renseigner et apprendre auprès d’enseignants, je ne vais pas vous apprendre que les journaux (télévisés ou non) n’ont pas toujours été une source fiable d’information.\r\nJe vous invite à lire l’article sur l’amour, le respect et la paix.', 3, 0),
(5, 'L''islam et ses parents', 'En tant que converti je vais essayer de donner des conseils à ceux qui pourraient en avoir besoin.\r\nMes parents étant chrétiens (pas très pratiquants), ma conversion a bien entendu été mal prise.\r\nMa plus grosse erreur a sans doute été de ne pas leur en parler lorsque je commençais à y porter de l’intérêt. Il faut leur en parler en douceur, parler de religion avec eux, de votre vision et pourquoi vous vous y intéressez. Aussi demandez-leur leur avis, même s’ils ne sont pas d’accord, vous pourrez discuter avec eux. Même si votre décision est prise, la moindre des choses c’est de mettre ses parents au courant. Les parents sont très importants dans l’islam, il ne faut pas les écarter.\r\n\r\nSi vous pouvez parlez-en en face à face avec eux, ou si vous ne pouvez pas à cause de la distance par exemple, le téléphone peut être une solution. \r\nN’hésitez pas à en discuter un long moment, ils comprendront mieux les raisons de votre choix. Vous pourrez aussi bien sûr en reparler plus tard, si vous sentez qu’il vaut mieux en rediscuter une autre fois faites-le, il est parfois utile de les laisser réfléchir.', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Conversion'),
(2, 'Coran'),
(3, 'Valeurs de l''Islam'),
(4, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(30) NOT NULL,
  `date_posted` int(11) NOT NULL,
  `content` text NOT NULL,
  `id_article` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `comment`
--

INSERT INTO `comment` (`id`, `author`, `date_posted`, `content`, `id_article`) VALUES
(1, 'test', 1308002697, 'Yooo !', 3),
(2, 'tetsté', 1308004061, 'fd', 3),
(3, 'fddf', 1308174169, 'é\r\néé\r\nhgfg', 5),
(4, 'l', 1308407095, 'ml', 1),
(5, 'l', 1308407128, 'ml', 1);

-- --------------------------------------------------------

--
-- Structure de la table `CommentMessage`
--

CREATE TABLE IF NOT EXISTS `CommentMessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `messageId` bigint(20) NOT NULL,
  `commentId` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `messageId` (`messageId`),
  KEY `commentId` (`commentId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `CommentMessage`
--

INSERT INTO `CommentMessage` (`id`, `messageId`, `commentId`) VALUES
(1, 1, 1),
(2, 1, 3),
(3, 3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `Comments`
--

CREATE TABLE IF NOT EXISTS `Comments` (
  `commentId` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`commentId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `Comments`
--

INSERT INTO `Comments` (`commentId`, `userId`, `content`, `date`) VALUES
(1, 1, 'mouhahaaha', '1990-11-01 13:37:00'),
(2, 1, 'Yeah', '0000-00-00 00:00:00'),
(3, 1, 'hum', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `FavoritesTags`
--

CREATE TABLE IF NOT EXISTS `FavoritesTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `tagId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `tagId` (`tagId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `FavoritesTags`
--


-- --------------------------------------------------------

--
-- Structure de la table `History`
--

CREATE TABLE IF NOT EXISTS `History` (
  `historyId` int(11) NOT NULL AUTO_INCREMENT,
  `topicId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `ipAddress` varchar(15) NOT NULL,
  `content` longtext NOT NULL,
  `date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`historyId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `History`
--

INSERT INTO `History` (`historyId`, `topicId`, `userId`, `ipAddress`, `content`, `date`) VALUES
(1, 1, 1, '33.133.188.12', '...', '0000-00-00 00:00:00'),
(2, 2, 1, '33.133.188.12', 'By the early 1990s, the popularity of theory as a subject of interest by itself was declining slightly (along with job openings for pure theorists) even as the texts of literary theory were incorporated into the study of almost all literature. As of 2004[update], the controversy over the use of theory in literary studies has all but died out, and discussions on the topic within literary and cultural studies tend now to be considerably milder and less acrimonious (though the appearance of volumes such as Theory''s Empire: An Anthology of Dissent, edited by Nathan Parker with Andrew Costigan, may signal a resurgence of the controversy). Some scholars draw heavily on theory in their work, while others only mention it in passing or not at all; but it is an acknowledged, important part of the study of literature.', '0000-00-00 00:00:00'),
(3, 3, 2, '80.155.18.10', 'arerzz', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `Messages`
--

CREATE TABLE IF NOT EXISTS `Messages` (
  `messageId` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `topicId` bigint(20) NOT NULL,
  `content` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vote` int(11) NOT NULL DEFAULT '0',
  `ipAddress` varchar(15) NOT NULL,
  PRIMARY KEY (`messageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `Messages`
--

INSERT INTO `Messages` (`messageId`, `userId`, `topicId`, `content`, `date`, `vote`, `ipAddress`) VALUES
(1, 1, 1, '1er message', '0000-00-00 00:00:00', 3, '80.155.18.10'),
(2, 1, 1, 'Salut', '0000-00-00 00:00:00', 8, '80.155.18.10'),
(3, 1, 2, 'coucou', '0000-00-00 00:00:00', 0, '80.155.18.10');

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `author` varchar(30) NOT NULL,
  `date_posted` int(11) NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `author`, `date_posted`, `last_updated`) VALUES
(1, 'Bienvenue', 'Le site est ouvert !', '', 1307970965, '2011-06-13 15:17:25');

-- --------------------------------------------------------

--
-- Structure de la table `Tags`
--

CREATE TABLE IF NOT EXISTS `Tags` (
  `tagId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`tagId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `Tags`
--

INSERT INTO `Tags` (`tagId`, `name`, `amount`) VALUES
(1, 'info', 3),
(2, 'choco', 24),
(3, 'film', 11),
(4, 'hum', 1),
(5, 'dd', 1);

-- --------------------------------------------------------

--
-- Structure de la table `Topic`
--

CREATE TABLE IF NOT EXISTS `Topic` (
  `topicId` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vote` int(11) NOT NULL DEFAULT '0',
  `lastEditTime` timestamp NULL DEFAULT NULL,
  `lastEditAuthor` int(11) DEFAULT NULL,
  `ipAddress` varchar(15) NOT NULL,
  PRIMARY KEY (`topicId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `Topic`
--

INSERT INTO `Topic` (`topicId`, `userId`, `title`, `message`, `date`, `vote`, `lastEditTime`, `lastEditAuthor`, `ipAddress`) VALUES
(1, 1, 'Test', 'Ceci est un test', '0000-00-00 00:00:00', 5, NULL, NULL, '80.155.18.10'),
(2, 1, 'Pouet', 'Pouet Pouetéé', '0000-00-00 00:00:00', 9, NULL, NULL, '80.155.18.10'),
(3, 1, 'Humm', 'huhu', '0000-00-00 00:00:00', 30, NULL, NULL, '80.155.18.10'),
(4, 1, 'LOlXDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDFFFFFFFFFFFFFFOKFJOKFOFJKOPFKJOFONFOJISDO', 'LOL', '2011-10-19 00:54:30', 0, NULL, NULL, '127.0.0.1'),
(5, 1, 'oiioo', 'oi', '2011-10-19 00:55:41', 0, NULL, NULL, '127.0.0.1');

-- --------------------------------------------------------

--
-- Structure de la table `TopicTag`
--

CREATE TABLE IF NOT EXISTS `TopicTag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topicId` bigint(20) NOT NULL,
  `tagId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topicId` (`topicId`),
  KEY `tagId` (`tagId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `TopicTag`
--

INSERT INTO `TopicTag` (`id`, `topicId`, `tagId`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 3, 1),
(5, 1, 3),
(6, 4, 4),
(7, 5, 5);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` int(11) DEFAULT NULL,
  `date_created` int(11) NOT NULL,
  `last_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `login`, `password`, `email`, `date_created`, `last_updated`) VALUES
(1, 'peekyou', '21232f297a57a5a743894a0e4a801fc3', NULL, 0, '2011-06-18 18:39:48');
