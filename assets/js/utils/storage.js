const SESSION_KEY = "fittrack_session";
const WORKOUTS_KEY = "fittrack_workouts";
const ROUTINES_KEY = "fittrack_routines";
const PROFILE_KEY = "fittrack_profile";
const PREFS_KEY = "fittrack_preferences";

const DEFAULT_PROFILE = {
  name: "Alex Rivera",
  email: "athlete@ironpulse.com",
  phone: "",
  city: "",
  gender: "",
  bio: "",
  goals: ["Muscle gain", "Hypertrophy"],
};

export function getSession() {
  try {
    const raw = localStorage.getItem(SESSION_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

export function setSession(data) {
  localStorage.setItem(SESSION_KEY, JSON.stringify(data));
}

export function clearSession() {
  localStorage.removeItem(SESSION_KEY);
}

export function getWorkouts() {
  try {
    const raw = localStorage.getItem(WORKOUTS_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

export function setWorkouts(list) {
  localStorage.setItem(WORKOUTS_KEY, JSON.stringify(list));
}

export function getRoutines() {
  try {
    const raw = localStorage.getItem(ROUTINES_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

export function setRoutines(list) {
  localStorage.setItem(ROUTINES_KEY, JSON.stringify(list));
}

export function getProfile() {
  try {
    const raw = localStorage.getItem(PROFILE_KEY);
    if (raw) {
      const parsed = JSON.parse(raw);
      const merged = {
        ...DEFAULT_PROFILE,
        ...parsed,
        goals:
          Array.isArray(parsed.goals) && parsed.goals.length
            ? parsed.goals
            : [...DEFAULT_PROFILE.goals],
      };
      delete merged.avatar;
      return merged;
    }
  } catch {
    /* ignore */
  }
  return { ...DEFAULT_PROFILE };
}

export function setProfile(obj) {
  localStorage.setItem(PROFILE_KEY, JSON.stringify(obj));
}

const DEFAULT_PREFS = {
  emailWeekly: true,
  pushPRs: true,
  coachMessages: false,
  units: "metric",
  compactCharts: false,
};

export function getPreferences() {
  try {
    const raw = localStorage.getItem(PREFS_KEY);
    if (raw) return { ...DEFAULT_PREFS, ...JSON.parse(raw) };
  } catch {
    /* ignore */
  }
  return { ...DEFAULT_PREFS };
}

export function setPreferences(obj) {
  localStorage.setItem(PREFS_KEY, JSON.stringify({ ...getPreferences(), ...obj }));
}
