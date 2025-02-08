CREATE OR REPLACE VIEW  items_view AS
SELECT i.id AS itemID, i.name AS itemName,i.category_id , c.id AS categoryID, c.name AS categoryName FROM items i
INNER JOIN categories c on i.category_id = c.id ;