# Extra exam for advanced staff supervision Part I
DELETE FROM WP_MODULE_RESOURCES
WHERE ID = 83;

# Fix Personalit7y Synergy title
UPDATE wp_quiz
SET name = "Personality Synergy"
WHERE name = "Personalit7y Synergy";

# Create Quiz for Achieving Supervisory Balance
INSERT INTO wp_quiz
(name, description, org_id, user_id, date_created, passing_score, num_attempts, time_limit)
VALUES ("Achieving Supervisory Balance", "Achieving Supervisory Balance", 692, 88, NOW(), NULL, 0, "00:20:00");

# Create Quiz for Masterful Meetings
INSERT INTO wp_quiz
(name, description, org_id, user_id, date_created, passing_score, num_attempts, time_limit)
VALUES ("Masterful Meetings", "Masterful Meetings", 692, 88, NOW(), NULL, 0, "00:20:00");


# Create exam for Shockingly Professional Talk, Part I
INSERT INTO wp_module_resources (module_id,resource_id,`type`,`order`)
SELECT modules.ID, quiz.ID, "exam", 50
from wp_quiz quiz
JOIN wp_modules modules
ON modules.title = quiz.name
WHERE modules.title = "Shockingly Professional Talk, Part I";

# Create exam for Shockingly Professional Talk, Part II
INSERT INTO wp_module_resources (module_id,resource_id,`type`,`order`)
SELECT modules.ID, quiz.ID, "exam", 50
from wp_quiz quiz
JOIN wp_modules modules
ON modules.title = quiz.name
WHERE modules.title = "Shockingly Professional Talk, Part II";

# Create exam for Medical Preparation For Burn Camps
INSERT INTO wp_module_resources (module_id,resource_id,`type`,`order`)
SELECT modules.ID, quiz.ID, "exam", 50
from wp_quiz quiz
JOIN wp_modules modules
ON modules.title = quiz.name
WHERE modules.title = "Medical Preparation For Burn Camps";

# Create exam for Awesome Archery, Part I
INSERT INTO wp_module_resources (module_id,resource_id,`type`,`order`)
SELECT modules.ID, quiz.ID, "exam", 50
from wp_quiz quiz
JOIN wp_modules modules
ON modules.title = quiz.name
WHERE modules.title = "Awesome Archery, Part I";

# Create exam for Awesome Archery, Part II
INSERT INTO wp_module_resources (module_id,resource_id,`type`,`order`)
SELECT modules.ID, quiz.ID, "exam", 50
from wp_quiz quiz
JOIN wp_modules modules
ON modules.title = quiz.name
WHERE modules.title = "Awesome Archery, Part II";

# Create exam for Personality Synergy
INSERT INTO wp_module_resources (module_id,resource_id,`type`,`order`)
SELECT modules.ID, quiz.ID, "exam", 50
from wp_quiz quiz
JOIN wp_modules modules
ON modules.title = quiz.name
WHERE modules.title = "Personality Synergy";

# Create exam for Achieving Supervisory Balance
INSERT INTO wp_module_resources (module_id,resource_id,`type`,`order`)
SELECT modules.ID, quiz.ID, "exam", 50
from wp_quiz quiz
JOIN wp_modules modules
ON modules.title = quiz.name
WHERE modules.title = "Achieving Supervisory Balance";

# Create exam for Masterful Meetings
INSERT INTO wp_module_resources (module_id,resource_id,`type`,`order`)
SELECT modules.ID, quiz.ID, "exam", 50
from wp_quiz quiz
JOIN wp_modules modules
ON modules.title = quiz.name
WHERE modules.title = "Masterful Meetings";

# Update the Active Lifeguarding exam. It was Anaphylactic Emergencies
UPDATE wp_module_resources
SET resource_id = 60
WHERE module_id = 9
AND type = "exam"





