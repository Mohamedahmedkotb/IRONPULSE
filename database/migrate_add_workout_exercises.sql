-- Run once on existing IronPulse MySQL DB (phpMyAdmin or mysql CLI).
-- Links library exercises to logged workouts.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS workout_exercises (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  workout_id INT UNSIGNED NOT NULL,
  exercise_id INT UNSIGNED NOT NULL,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  sets SMALLINT UNSIGNED NOT NULL DEFAULT 3,
  reps SMALLINT UNSIGNED NOT NULL DEFAULT 10,
  weight_kg DECIMAL(7,2) NULL,
  rest_seconds SMALLINT UNSIGNED NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_workout_exercise (workout_id, exercise_id),
  KEY idx_we_workout (workout_id),
  KEY idx_we_exercise (exercise_id),
  CONSTRAINT fk_we_workout FOREIGN KEY (workout_id) REFERENCES workouts (id) ON DELETE CASCADE,
  CONSTRAINT fk_we_exercise FOREIGN KEY (exercise_id) REFERENCES exercises (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
