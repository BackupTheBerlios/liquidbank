
CREATE TABLE tb_config (
	label 		VARCHAR(255),
	value		VARCHAR(255)
);

CREATE TABLE tb_user (
	id			VARCHAR(255) PRIMARY KEY,
	registered 	BOOLEAN	DEFAULT false,
	update_stamp DATETIME,
	cache_stamp DATETIME
	graph_stamp DATETIME
);

CREATE TABLE tb_transaction (
	id			VARCHAR(255) PRIMARY KEY,
	user_src	VARCHAR(255),
	user_dst 	VARCHAR(255),
	amount		DECIMAL(14,2)	DEFAULT 0,
	title		VARCHAR(255),
	stamp		DATETIME DEFAULT now
);

CREATE TABLE tb_cache (
    id	VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255),
    created_on	DATETIME,
    expired_on	DATETIME
);

CREATE VIEW tb_transaction_graph AS SELECT count(*) as ct,user_src,user_dst,sum(amount) as amount_sum FROM tb_transaction GROUP BY user_src,user_dst;
