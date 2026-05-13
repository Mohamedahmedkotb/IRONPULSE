SET NAMES utf8mb4;

-- Demo user: email demo@ironpulse.com / password: password
-- (bcrypt hash compatible with PHP password_verify)
INSERT INTO users (id, username, full_name, email, password_hash, gender, fitness_goal, role, xp, streak, bio, city)
VALUES
(1, 'demouser', 'Demo Athlete', 'demo@ironpulse.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Prefer not to say', 'Strength', 'member', 1200, 7, 'Training for consistency.', 'Austin');

INSERT INTO coaches (id, name, specialty, bio, image, rating) VALUES
(1, 'Sarah Jenkins', 'Strength & conditioning', 'Olympic lifting and hypertrophy specialist.', 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?auto=format&fit=crop&w=600&q=80', 4.90),
(2, 'Marcus Cole', 'Endurance coach', 'Cycling and running programming.', 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?auto=format&fit=crop&w=600&q=80', 5.00),
(3, 'Elena Park', 'Mobility & recovery', 'Yoga and rehab-focused sessions.', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=600&q=80', 4.80),
(4, 'James Okonkwo', 'Powerlifting', 'Meet prep and technique.', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=600&q=80', 4.95),
(5, 'Nina Alvarez', 'Hybrid athlete', 'Cross-training for busy pros.', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=600&q=80', 4.85),
(6, 'David Chen', 'Corrective exercise', 'Desk athletes and posture.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80', 4.90);

INSERT INTO exercises (name, category, muscle_group, difficulty, instructions, image) VALUES
('Barbell bench press', 'Strength', 'Chest', 'Intermediate', 'Retract scapula, control eccentric, press vertically.', 'photo-1534438327276-14e5300c3a48'),
('Incline dumbbell press', 'Strength', 'Chest', 'Intermediate', 'Upper chest bias with free shoulder motion.', 'photo-1571019614242-c5c5dee9f50b'),
('Pull-up', 'Strength', 'Back', 'Advanced', 'Full hang to chest-to-bar trajectory.', 'photo-1598971639058-fab3c3109a00'),
('Barbell row', 'Strength', 'Back', 'Intermediate', 'Hinge-supported horizontal pull.', 'photo-1517836357463-d25dfeac3438'),
('Back squat', 'Strength', 'Legs', 'Intermediate', 'Knee and hip extension strength.', 'photo-1574680096141-d9b3b8c8e5c4'),
('Romanian deadlift', 'Strength', 'Legs', 'Intermediate', 'Hamstring and glute bias hinge.', 'photo-1517963879433-6ad2b056d712'),
('Overhead press', 'Strength', 'Shoulders', 'Intermediate', 'Vertical press with stacked joints.', 'photo-1541534741688-6078c6bfb5c0'),
('Barbell curl', 'Isolation', 'Arms', 'Beginner', 'Elbow flexion with stable shoulders.', 'photo-1583454156664-26d3d27b0fa2'),
('Cable crunch', 'Core', 'Core', 'Beginner', 'Spinal flexion under load.', 'photo-1518611012118-696072aa579a'),
('Bench press', 'Strength', 'Chest', 'Intermediate', 'Standard flat bench press.', 'photo-1534438327276-14e5300c3a48'),
('Deadlift', 'Strength', 'Back', 'Advanced', 'Conventional or sumo pull from floor.', 'photo-1517836357463-d25dfeac3438'),
('Walking lunge', 'Strength', 'Legs', 'Beginner', 'Controlled step length and torso.', 'photo-1574680096141-d9b3b8c8e5c4'),
('Cable row', 'Strength', 'Back', 'Beginner', 'Seated horizontal pull.', 'photo-1517836357463-d25dfeac3438');

INSERT INTO workouts (user_id, title, category, duration, calories, workout_date, notes) VALUES
(1, 'Upper power', 'Push', 62, 420, CURDATE(), 'Top set RPE 8'),
(1, 'Zone 2 run', 'Cardio', 45, 380, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '5km easy');

INSERT INTO routines (user_id, title, description) VALUES
(1, 'Upper A — Strength', 'Heavy compounds + accessories.');

SET @rid = LAST_INSERT_ID();
INSERT INTO routine_exercises (routine_id, exercise_id, sort_order, sets, reps)
SELECT @rid, id, 1, 4, 6 FROM exercises WHERE name = 'Bench press' LIMIT 1;
INSERT INTO routine_exercises (routine_id, exercise_id, sort_order, sets, reps)
SELECT @rid, id, 2, 4, 8 FROM exercises WHERE name = 'Pull-up' LIMIT 1;

INSERT INTO meal_plans (user_id, plan_date, title, breakfast, lunch, dinner, calories, protein_g, carbs_g, fats_g) VALUES
(1, CURDATE(), 'Daily template', 'Oats + berries', 'Chicken bowl', 'Salmon + rice', 3180, 180, 320, 90);

INSERT INTO progress_logs (user_id, weight, body_fat, bmi, log_date) VALUES
(1, 82.5, 14.5, 24.2, CURDATE());

INSERT INTO notifications (user_id, type, title, message, is_read) VALUES
(1, 'welcome', 'Welcome to Ironpulse', 'Your dashboard is ready. Log your first workout!', 0);
