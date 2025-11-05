-- Creates a second interviewer account with the same default password hash used in update_admin_password.sql
-- Change the email/name values if you prefer.

INSERT INTO users (email, password, first_name, last_name, user_type, status, created_at, updated_at)
VALUES (
  'interviewer2@rsd.com',
  '$2y$10$piu2Lxkkt/8cJeOcehBGwuHf9EJdvw3qFLV31zGOOFnMi.E1pyxaC',
  'Interviewer',
  'Two',
  'interviewer',
  'active',
  NOW(),
  NOW()
);
