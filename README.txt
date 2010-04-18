echo "SELECT user_src,sum(amount) FROM tb_transaction GROUP BY user_src;" | sqlite3 twitbank.sqlite3

quantité de monnaie distribuée par les participants, par participant

echo "SELECT user_dst,sum(amount) FROM tb_transaction GROUP BY user_dst;" | sqlite3 twitbank.sqlite3

quantité de monnaie recue par les participants, par participant
