-- 1. Thêm cột 'role' vào bảng 'users'
ALTER TABLE users
ADD role VARCHAR(50) NOT NULL DEFAULT 'user';

-- 2. Tự cấp cho mình quyền Admin (thay 'tencuaban' bằng username của bạn)
UPDATE users
SET role = 'admin'
WHERE username = 'tencuaban';