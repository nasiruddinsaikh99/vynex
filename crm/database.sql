-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 21, 2025 at 07:24 PM
-- Server version: 8.0.44-0ubuntu0.22.04.1
-- PHP Version: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `syntrex`
--

-- --------------------------------------------------------

--
-- Table structure for table `crm`
--

CREATE TABLE `crm` (
  `id` int NOT NULL,
  `website_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `website_name` text,
  `facebook_page` text,
  `website_phone` text,
  `email_from` text,
  `website_active` text,
  `start_date` text,
  `country` text,
  `profession` text,
  `industry` text,
  `trade` text,
  `member_type` text,
  `currency_code` text,
  `status` int DEFAULT '0',
  `email_status` text NOT NULL,
  `secure_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `email_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `crm`
--

INSERT INTO `crm` (`id`, `website_id`, `url`, `email`, `website_name`, `facebook_page`, `website_phone`, `email_from`, `website_active`, `start_date`, `country`, `profession`, `industry`, `trade`, `member_type`, `currency_code`, `status`, `email_status`, `secure_url`, `email_name`) VALUES
(208, '36382', 'www.gonewtownabbey.com', 'info@gonewtownabbey.com', 'Go Newtownabbey', 'https://www.facebook.com/GoNewtownabbey/', '07468434972', 'samplesite.com', '1', '20230605102814', 'GB', 'Local Business', 'Newtownabbey', 'Local Business', 'Local Business', 'GBP', 0, 'valid', 'www.gonewtownabbey.com', 'startadirectory.com'),
(856, '11359', 'www.ebonyexchange.com', 'info@ebonyexchange.com', 'Ebony Exchange', 'http://www.facebook.com/ebonyexchange', '804-506-0665', 'ebonyexchange.com', '1', '', 'US', 'Richmond\'s Black Online Community', 'Community', 'Richmond\'s Black Online Community', 'Local Business', 'USD', 0, 'invalid', 'ww2.securemypayment.com', 'startadirectory.com'),
(858, '11492', 'www.fundexa.online', 'team@fundexa.com', 'FUNDEXA', 'https://www.facebook.com/Fundexa-263078750775124/', '', 'fundexa.com', '1', '', 'AU', 'Investor Professionals', 'Investor Relations', 'Investor Professionals', 'Local Business', 'AUD', 0, 'valid', 'www.fundexa.online', 'startadirectory.com'),
(863, '11666', 'www.nearme.bar', 'support@nearmedirectories.com', 'Bars Near Me', '', '', 'nearme.bar', '1', '', 'US', 'Bar', 'Bar', 'Bar', 'Local Business', 'USD', 0, 'invalid', 'www.nearme.bar', 'startadirectory.com'),
(960, '12201', 'www.businessja.com', 'cclarkepersonal@gmail.com', 'BusinessJA', 'https://www.facebook.com/OnlineBusinessDirectory876/', '18766274901', 'businessja.com', '1', '', 'JM', 'Local Business', 'Local Business', 'Local Business', 'Local Business', 'USD', 0, 'unknown', 'www.businessja.com', 'startadirectory.com'),
(1405, '14761', 'www.broome.art', 'info@Broome.ART', 'Broome.ART', '', '', 'Broome.ART', '1', '', 'US', 'Artist Painting Self Portraits', 'Broome Art', 'Artist Painting Self Portraits', 'Local Business', 'USD', 0, 'unknown', 'www.broome.art', 'startadirectory.com'),
(1407, '15532', 'www.cannabisworldwide.directory', 'support@cannabisworldwide.directory', 'Cannabis Worldwide Directory', 'https://www.facebook.com/CannabisWorldwidedirectory-364190494339832/', '', 'cannabisworldwide.directory', '1', '', 'US', 'Cannabis | Medical Cannabis | Marijuana | Psychoactive drug from the Cannabis plant used for medical or recreational purposes', 'Directory of Cannabis Worldwide | Growers | Producers | Distributors | Educators | Lawyers and Products', 'Cannabis | Medical Cannabis | Marijuana | Psychoactive drug from the Cannabis plant used for medical or recreational purposes', 'Local Business', 'USD', 0, 'valid', 'www.cannabisworldwide.directory', 'startadirectory.com'),
(1428, '15533', 'www.doctorsworldwide.directory', 'support@doctorsworldwide.directory', 'Doctors Worldwide Directory of Medicine Profession', 'https://www.facebook.com/doctorsworldwide.directory', '', 'doctorsworldwide.directory', '1', '', 'US', 'Doctors & Specialists Worldwide', 'Doctors | Physicians | Surgeons | Specialists & Allied Professionals Worldwide', 'Doctors & Specialists Worldwide', 'Local Business', 'USD', 0, 'valid', 'www.doctorsworldwide.directory', 'startadirectory.com'),
(1432, '15502', 'www.educationalliance.com.au', 'info@www.educationalliance.com.au', 'Education Alliance', '', '', 'www.educationalliance.com.au', '1', '', 'AU', 'Education Alliance', 'Education', 'Education Alliance', 'Local Business', 'AUD', 0, 'accept_all_unverifiable', 'www.educationalliance.com.au', 'startadirectory.com'),
(1436, '15836', 'www.fashiondesigners.directory', 'support@FashionDesigners.Directory', 'Fashion Designers Directory for Worldwide Fashion Designers', 'https://www.facebook.com/Fashion-Designers-Directory-1034099066777199/', '', 'FashionDesigners.Directory', '1', '', 'US', 'Fashion Designers Directory', 'Fashion Designers | Apparel | Fashion Jewelry Directory', 'Fashion Designers Directory', 'Local Business', 'USD', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(1451, '14891', 'www.marineandboat.directory', 'info@marineandboat.directory', 'Marine and Boat', 'http://www.facebook.com/marineandboat.directory', '(530) 345-5165', 'marineandboat.directory', '1', '', 'US', 'Industry Members', 'Marine and Boat Industry', 'Industry Members', 'Local Business', 'USD', 0, 'valid', 'www.marineandboat.directory', 'startadirectory.com'),
(1464, '15613', 'www.nearme.catering', 'support@nearmedirectories.com', 'Catering Near Me', '', '', 'nearme.catering', '1', '', 'US', 'Caterer', 'Catering', 'Caterer', 'Local Business', 'USD', 0, 'valid', 'www.nearme.catering', 'startadirectory.com'),
(1467, '16021', 'www.parentcoach.club', 'info@parentguide.ca', 'Parent Coach Club', 'https://www.facebook.com/ParentCoachClub', '519-645-7342', 'parentguide.ca', '1', '', 'US', 'Parent Coaches', 'Parent Coaches', 'Parent Coaches', 'Local Business', 'USD', 0, 'valid', 'www.parentcoach.club', 'startadirectory.com'),
(1468, '15837', 'www.payingguest.directory', 'support@payingguest.directory', 'Paying Guest the Leading source for finding Rooms & Providers', 'https://www.facebook.com/Paying-Guest-Directory-390360511800963/', '', 'payingguest.directory', '1', '', 'US', 'Paying Guest Directory, Room rental made easy', 'Paying Guest Provider & Finders', 'Paying Guest Directory, Room rental made easy', 'Local Business', 'USD', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(1469, '15730', 'www.personaltouch.directory', 'info@personaltouch.directory', 'Personal Touch Massage', 'https://www.facebook.com/Personal-Touch-Massage-325276858328904', '', 'personaltouch.directory', '1', '', 'US', 'Male Massage', 'Gay Friendly Male Massage', 'Male Massage', 'Local Business', 'USD', 0, 'valid', 'www.personaltouch.directory', 'startadirectory.com'),
(1488, '15063', 'tt.directory', 'info@tt.directory', 'TT DIRECTORY', 'https://www.facebook.com/tt.directory', '', 'tt.directory', '1', '', 'TT', 'Trinidad & Tobago Businesses & Professionals', 'Trinidad & Tobago Local Listings', 'Trinidad & Tobago Businesses & Professionals', 'Local Business', 'USD', 0, 'valid', 'www.tt.directory', 'startadirectory.com'),
(1490, '15535', 'www.vacationsworldwide.directory', 'support@VacationsWorldwide.directory', 'Vacations Worldwide.directory A Vacation & Travel Worldwide Network', 'https://www.facebook.com/VacationsWorldwide.directory', '', 'VacationsWorldwide.directory', '1', '', 'US', 'Vacations & Travel  Worldwide Network', 'Vacations & Travel Worldwide Network', 'Vacations & Travel  Worldwide Network', 'Local Business', 'USD', 0, 'valid', 'www.vacationsworldwide.directory', 'startadirectory.com'),
(1508, '12758', 'www.dentistworldwide.directory', 'support@dentistworldwide.directory', 'Dentist Worldwide Directory Listings of Dental Practices, Dentists and Dental Care Manufactures | Distributors | Suppliers Worldwide.', 'https://www.facebook.com/Dentists-Worldwide-Directory-423720544749257/', '', 'DentistWorldwide.Directory', '1', '', 'US', 'Dentist and Dental Equipment Companies and Suppliers Worldwide', 'Dentist Directory and Dental Equipment Companies Worldwide', 'Dentist Worldwide Directory', 'Local Business', 'USD', 0, 'invalid', 'ww2.securemypayment.com', 'startadirectory.com'),
(1510, '12759', 'www.eventsworldwide.directory', 'support@EventsWorldwide.Directory', 'Events Worldwide directory the leading Events Directory Worldwide for Music Festivals|Venues|Conferences|Trade | Fashion Shows|Auditions|Hackathons|Contests|Political Rallies|Fundraisers|Gaming Competitions', 'https://www.facebook.com/Events-Worldwide-Directory-1812796462145124/', '', 'EventsWorldwide.Directory', '1', '', 'US', 'Events Worldwide a Directory of Events Worldwide Post | Find | Attend | Advertise your Events with  Events Worldwide', 'Events Worldwide Directory | All Kind of Events | Find or Post & Attend Events Worldwide', 'Events Worldwide a Directory of Events Worldwide Post | Find | Attend | Advertise your Events with  Events Worldwide', 'Local Business', 'USD', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(1521, '12622', 'www.hotelsworldwide.directory', 'support@hotelsworldwide.directory', 'Hotels Worldwide Directory Leading Hotels |Resorts | Destinations | Luxury Villas | Spas | Fitness & Vacation Facilities Worldwide', 'https://www.facebook.com/Hotel-Worldwide-940633282762933/', '', 'hotelsworldwide.directory', '1', '', 'US', 'Hotels Worldwide Directory for Luxury Hotels | Resorts & Luxury Vacation Properties Worldwide', 'Hotels Resorts Luxury Villas and Vacation Facilities Directory Worldwide', 'Hotels Worldwide Directory for Luxury Hotels | Resorts & Luxury Vacation Properties Worldwide', 'Local Business', 'USD', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(1526, '12776', 'www.kyokushinkai.online', 'info@kyokushinkai.online', 'Kyokushinkai in the world', '', '1-800-000-0000', 'kyokushinkai.online', '1', '', 'DE', 'Kyokushinkai', 'Kyokushinkai', 'Kyokushinkai', 'Local Business', 'EUR', 0, 'unknown', 'ww2.securemypayment.com', 'startadirectory.com'),
(1541, '12915', 'www.tea.directory', 'info@tea.directory', 'Tea Directory', '', '', 'tea.directory', '1', '', 'GB', 'Tea', 'Food', 'Tea', 'Local Business', 'GBP', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(1573, '6570', 'www.seniorveterans.care', 'referrals@seniorveterans.care', 'Senior Veterans Care Network', 'https://www.facebook.com/aplaceforvets/', '1-929-367-8387', 'seniorveterans.care', '1', '', 'US', 'Aid & Attendance VA Benefits , Veterans Home Care and Veterans Assisted Living', 'Veterans Aid & Attendance, Veterans Assisted Living and Veterans  Home Care', 'Aid & Attendance VA Benefits , Veterans Home Care and Assisted Living', 'Local Business', 'USD', 0, 'valid', 'www.seniorveterans.care', 'startadirectory.com'),
(1574, '4574', 'www.site.cards', 'jliles@adviceinteractive.com', 'Site Cards', 'https://www.facebook.com/AdviceLocal', '877-692-7250', 'advicelocal.com', '1', '', 'US', 'Site Card', 'Local Business Reviews', 'Site Card', 'Local Business', 'USD', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(1641, '5688', 'www.best-divorce.attorney', 'info@best-divorce.attorney', 'Best-Divorce.Attorney', 'http://www.facebook.com', '(888) 385-4245 or (210) 338-8150', 'best-divorce.attorney', '1', '', 'US', 'Divorce Attorney', 'Attorney', 'Attorney', 'Local Business', 'USD', 0, 'accept_all_unverifiable', 'ww2.securemypayment.com', 'startadirectory.com'),
(1651, '6002', 'www.caribbeanbusiness.directory', 'info@caribbeanbusiness.directory', 'Caribbean Business Directory', '', '', 'caribbeanbusiness.directory', '1', '', 'TT', 'Business', 'Business', 'Business', 'Local Business', 'USD', 0, 'valid', 'www.CARIBBEANBUSINESS.DIRECTORY', 'startadirectory.com'),
(1693, '5397', 'www.kitsapmarketplace.directory', 'tracy@kitsapmarketplace.com', 'Kitsap Marketplace', 'http://www.facebook.com/kitsapmarketplace', '(888) 896-6696', 'kitsapmarketplace.directory', '1', '', 'US', 'Kitsap Local Businesses', 'Connecting Business with Community', 'Kitsap Local Businesses', 'Local Business', 'USD', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(1699, '5462', 'www.londonmassage.directory', 'info@londonmassage.directory', 'The London Massage Directory', 'https://bit.ly/2Cpduyo', '07463994559', 'londonmassage.directory', '1', '', 'GB', 'Spa', 'Spa and Wellness', 'Spa', 'Local Business', 'GBP', 0, 'invalid', 'www.londonmassage.directory', 'startadirectory.com'),
(1779, '9846', 'dental.directory', 'info@dental.directory', 'Dental Directory', '', '', 'dental.directory', '1', '', 'US', '', '', '', 'Local Business', 'USD', 0, 'accept_all_unverifiable', 'www.dental.directory', 'startadirectory.com'),
(1815, '9721', 'www.localoffers.direct', 'admin@localoffers.direct', 'LOCALOFFERS.direct', '', '', 'localoffers.direct', '1', '', 'US', 'Stores, trade services and Local businesses', 'Online Offers', 'Stores, trade services and Local businesses', 'Local Business', 'GBP', 0, 'unknown', 'www.localoffers.direct', 'startadirectory.com'),
(2025, '3456', 'www.gsmart.club', 'info@gsmart.club', 'Gsmart.club', 'http://www.facebook.com/Gsmart.club', '8645078283', 'gsmart.club', '1', '', 'US', 'Gas Stations - Convenience Stores & Liquor Stores', 'Gas Stations - Convenience Stores & Liquor Stores', 'Gas Stations - Convenience Stores & Liquor Stores', 'Local Business', 'USD', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(2037, '2138', 'www.showcasedirectory.energy', 'info@energynow.com', 'EnergyNow SHOWCASE Digital Directory', 'https://www.facebook.com/EnergyNowNewsCriticalData', '', 'energynow.ca', '1', '', 'CA', '', 'Energy', '', 'Local Business', 'CAD', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(2066, '12247', 'www.realestateworldwide.directory', 'support@realestateworldwide.directory', 'Real Estate Worldwide | Real Estate & Mortgage Brokers and Allied Services Network', 'https://www.facebook.com/RealEstateworldwide.directory', '', 'realestateworldwide.directory', '1', '', 'US', 'Real Estate Worldwide Directory | Leading Network of Realtors, Mortgage Brokers & Allied Businesses', 'Real Estate Agents, Mortgage Brokers and Allied Business Listings Worldwide', 'Real Estate Worldwide Directory | Leading Network of Realtors, Mortgage Brokers & Allied Businesses', 'Local Business', 'USD', 0, 'accept_all_unverifiable', 'ww2.securemypayment.com', 'startadirectory.com'),
(2077, '11863', 'www.universityandcollegedirectory.online', 'info@universityandcollegedirectory.online', 'universityandcollegedirectory.online', 'https://www.facebook.com/University-and-College-Directory-Online-121108155235159/', '+44 (0)203 815 8019', 'universityandcollegedirectory.online', '1', '', 'US', 'Universities and Colleges', 'Directory of University and Colleges Online', 'Universities and Colleges', 'Local Business', 'GBP', 0, 'accept_all_unverifiable', 'www.universityandcollegedirectory.online', 'startadirectory.com'),
(2080, '12142', 'www.woo.live', 'ad@woo.live', 'WOO.live', 'https://www.facebook.com/Woo.live.Official', '+30 6979817554', 'woo.live', '2', '', 'GR', 'Woo', 'Woo', 'Woo', 'Local Business', 'EUR', 0, 'accept_all_unverifiable', 'www.woo.live', 'startadirectory.com'),
(2119, '23289', 'www.finditguide.com', 'info@finditguide.com', 'The Find-It Guide', 'https://www.facebook.com/FinditGuide/', '+49 (0) 6371 98 09 050', 'finditguide.com', '1', '20210407132645', 'DE', 'What are YOU trying to find?', 'Military Information, Local Businesses, Cars, Properties, Classifieds and more!', 'What are YOU trying to find?', 'Local Business', 'EUR', 0, 'valid', 'www.finditguide.com', 'startadirectory.com'),
(2120, '16527', 'www.bestmedicalmarijuanadoctors.com', 'medicalcards@bestmedicalmarijuanadoctors.com', 'Best MMJ Doctors', '', '', 'bestmedicalmarijuanadoctors.com', '1', '', 'US', 'Medical Marijuana Card', 'Medical Marijuana Doctors', 'Medical Card Evaluations', 'Local Business', 'USD', 0, 'accept_all_unverifiable', 'www.bestmedicalmarijuanadoctors.com', 'startadirectory.com'),
(2121, '17559', 'www.cupcakemonster.com', 'cupcakemonster@cupcakemonster.com', 'Cupcake Monster', '', '', 'cupcakemonster.com', '1', '20190916110839', 'US', 'Cupcake Bakeries, Cakes Bakery, & Desserts Shops', 'Bakeries, Desserts', 'Cupcake Bakeries, Cakes Bakery, & Desserts Shops', 'Local Business', 'USD', 0, 'valid', 'www.cupcakemonster.com', 'startadirectory.com'),
(2122, '29203', 'www.drpr4.com', 'pr4reports@drpr4.com', 'Dr. PR-4', '', '', 'drpr4.com', '1', '20220221111019', 'US', 'P&S PR4 Report', 'Workers Comp P&S Permanent and Stationary Reports', 'P&S PR4 Reports', 'Local Business', 'USD', 0, 'valid', 'www.drpr4.com', 'startadirectory.com'),
(2123, '21182', 'www.handicapmd.com', 'help@handicapmd.com', 'HandicapMD', '', '(833) DMV-3825', 'handicapmd.com', '1', '20201013123312', 'US', 'DMV Handicap Parking Placard Online', 'DMV Disabled Parking Permit Online', 'DMV Handicap Parking Placard Online', 'Local Business', 'USD', 0, 'valid', 'www.handicapmd.com', 'startadirectory.com'),
(2124, '16528', 'happymd.org', 'mmjcards@happymd.org', 'HappyMD', 'https://www.facebook.com/myhappymd', '831-454-6257', 'happymd.org', '1', '', 'US', 'Medical Marijuana Doctors', 'Medical Marijuana Card', 'Medical Marijuana Doctors', 'Local Business', 'USD', 0, 'accept_all_unverifiable', 'www.happymd.org', 'startadirectory.com'),
(2125, '16529', 'www.onelovemd.org', 'mmjcards@onelovemd.org', 'OneLoveMD', 'https://www.facebook.com/Onelovemd-Online-420-Evaluations-1879119402113469/', '', 'onelovemd.org', '1', '', 'US', 'Medical Marijuana Doctors', 'Medical Card Online', 'Medical Marijuana Doctors', 'Local Business', 'USD', 0, 'accept_all_unverifiable', 'www.onelovemd.org', 'startadirectory.com'),
(2126, '17502', 'www.weedmonster.com', 'whatsup@weedmonster.com', 'Weed Monster', '', '', 'weedmonster.com', '1', '20190904114820', 'US', 'Marijuana Dispensaries, Delivery, Doctors', 'Find Marijuana Near Me', 'Marijuana Dispensaries, Delivery, Doctors', 'Local Business', 'USD', 0, 'valid', 'www.weedmonster.com', 'startadirectory.com'),
(2127, '17558', 'www.workerscompdoctorstv.com', 'info@workerscompdoctorstv.com', 'Workers Comp Doctors TV', '', '', 'workerscompdoctorstv.com', '1', '20190916110839', 'US', 'Workers Comp Doctors', 'Workers Compensation Doctors', 'Workers Comp Doctors', 'Local Business', 'USD', 0, 'valid', 'www.workerscompdoctorstv.com', 'startadirectory.com'),
(2130, '35426', 'www.50statesoftherapy.com', '50statesoftherapy@gmail.com', '50 States of Therapy', '', '(760) 454-5555', 'samplesite.com', '1', '20230328110707', 'US', 'Therapist', 'Therapy', 'Therapist', 'Local Business', 'USD', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),
(2131, '35719', 'www.a1zoom.com', 'info@a1zoom.com', 'A1Zoom', 'https://www.facebook.com/groups/453739647110395/', '', 'a1zoom.com', '1', '20230417163044', 'CA', 'Canadian Business Owners', 'Canadian Business Owners', 'Canadian Business Owners', 'Local Business', 'CAD', 0, 'valid', 'www.a1zoom.com', 'startadirectory.com'),
(2132, '37147', 'www.abuntal.com', 'admin@abuntal.com', 'AbunTal-abundant talent', 'https://www.facebook.com/profile.php?id=100091641104826', '(403) 461-7849', 'samplesite.com', '1', '20230630110941', 'CA', 'Community', 'Professional', 'Community', 'Local Business', 'USD', 0, 'unknown', 'www.abuntal.com', 'startadirectory.com'),
(2133, '36200', 'www.acenational.org', 'info@acenational.org', 'Ace National', '', '1 (202) 800-9109', 'acenational.org', '1', '20230525085306', 'US', '', 'ACE National', '', 'Local Business', 'USD', 0, 'valid', 'www.acenational.org', 'startadirectory.com'),
(2134, '36314', 'www.aefreelance.com', 'support@aefreelance.com', 'AEFreelance.com', 'https://www.facebook.com/AEFreelanceFB', '', 'aefreelance.com', '1', '20230601033829', 'AE', 'Freelancer', 'Freelancing', 'Freelancer', 'Local Business', 'AED', 0, 'valid', 'ww2.securemypayment.com', 'startadirectory.com'),

-- --------------------------------------------------------

--
-- Table structure for table `crm_notes`
--

CREATE TABLE `crm_notes` (
  `crm_id` int NOT NULL,
  `notes` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crm`
--
ALTER TABLE `crm`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_notes`
--
ALTER TABLE `crm_notes`
  ADD PRIMARY KEY (`crm_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crm`
--
ALTER TABLE `crm`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11158;
COMMIT;
