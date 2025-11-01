ALTER TABLE orders
ADD payment_method VARCHAR(50) NOT NULL DEFAULT 'cod' AFTER status;