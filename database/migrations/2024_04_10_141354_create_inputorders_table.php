<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inputorders', function (Blueprint $table) {
            $table->id();
            //$table->integer('IdSuplier');
            //$table->integer('IdProduct');
            //$table->integer('IdQuote');
            $table->boolean('Status')->default(false); // Sirve para Validar la entrega, es decir, si ya la recibio el area y si corresponde con lo pedido
            $table->datetime('QuoteDate')->nullable();//sirve para almacenar cuando se pidió por el área

            $table->datetime('OrderDate')->nullable();
            $table->datetime('ReceivedDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('RequieredDate')->nullable();
            $table->decimal('Quantity',10,2);
            $table->string('UnitMeasurement');
            $table->string('Description')->nullable();//Concatenar IdProduct-IdQuote-description
            // el siguiente es el identificador de la orden desde el businness central
            $table->string('Fk_DocumentNoBC')->nullable();
            $table->bigInteger('Fk_LineNoBC')->nullable();
           // Crea atributo de llave foránea
            // Permitir valores NULL en IdProduct
            $table->foreignId('IdProduct')->nullable()->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();

           // Crea atributo de llave foránea
            $table->foreignId('IdSupplier')->constrained('suppliers')->cascadeOnUpdate()->cascadeOnDelete();
            // Crea atributo de llave foránea
            $table->foreignId('IdQuote')->constrained('quotes');
            $table->foreignId('IdArea')->constrained('areas')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });

        // Trigger para insertar al stock las cantidades ingresadas
        DB::unprepared("
            CREATE OR ALTER TRIGGER trg_UpdateStockQuantity
            ON dbo.inputorders
            AFTER INSERT, UPDATE
            AS
            BEGIN
                SET NOCOUNT ON;
                
                -- Verificar si el atributo Status es TRUE
                IF EXISTS (SELECT 1 FROM inserted WHERE Status = 1)
                BEGIN
                    -- Actualizar la tabla stocks con la nueva cantidad
                    UPDATE s
                    SET s.Quantity = s.Quantity + i.Quantity
                    FROM dbo.stocks s
                    INNER JOIN inserted i ON s.IdProduct = i.IdProduct AND s.IdArea = i.IdArea;

                    -- Actualizar LeadTime por cada producto
                    UPDATE products
                    SET LeadTime = ISNULL((
                        SELECT AVG(DATEDIFF(day, io.OrderDate, io.ReceivedDate)) AS AvgDaysToDeliver
                        FROM inputorders io
                        WHERE io.IdProduct = products.id
                    ), 7);
                    
                    -- Declaraciones para actualizar el Estado
                    DECLARE @IdProduct INT;
                    DECLARE @IdArea INT;
                    DECLARE @Disponibilidad DECIMAL;
                    DECLARE @LeadTime DECIMAL;
                    DECLARE @Estado NVARCHAR(15);

                    SET @IdProduct = (SELECT TOP 1 IdProduct FROM inserted);
                    SET @IdArea = (SELECT TOP 1 IdArea FROM inserted);

                    SET @Disponibilidad = (
                        (SELECT Quantity 
                        FROM stocks 
                        WHERE IdProduct = @IdProduct AND IdArea = @IdArea) * 360
                    ) / (
                        SELECT ISNULL(SUM(Quantity), 1) AS TotalQuantityConsumed
                        FROM outputorders
                        WHERE IdProduct = @IdProduct 
                        AND IdArea = @IdArea
                        AND CreatedDate >= DATEADD(day, -360, GETDATE())
                    );

                    SET @LeadTime = (SELECT LeadTime FROM products WHERE id = @IdProduct);

                    SET @Estado = 
                        CASE 
                            WHEN @Disponibilidad <= 0 THEN 'Sin stock'
                            WHEN @Disponibilidad > 0 AND @Disponibilidad <= (@LeadTime + 1) THEN 'Se agotará'
                            WHEN @Disponibilidad > (@LeadTime + 1) AND @Disponibilidad <= (@LeadTime + 10) THEN 'Pedir'
                            ELSE 'Disponible'
                        END;

                    -- Actualizar el atributo Status y EstimatedDurability
                    UPDATE stocks
                    SET
                        Status = @Estado,
                        EstimatedDurability = @Disponibilidad
                    WHERE IdProduct = @IdProduct AND IdArea = @IdArea;
                END;
            END;
        ");
        //trigger para eliminar del stock cantidades ingresadas al stock
        DB::unprepared("
            CREATE OR ALTER TRIGGER trg_UpdateStockQuantityOnDelete
            ON dbo.inputorders
            AFTER DELETE
            AS
            BEGIN
                SET NOCOUNT ON;

                -- Verificar si el elemento a eliminar tiene Status = 1
                IF EXISTS (SELECT 1 FROM deleted WHERE Status = 1)
                BEGIN
                    -- Actualizar la tabla stocks restando la cantidad eliminada
                    UPDATE s
                    SET s.Quantity = s.Quantity - d.Quantity
                    FROM dbo.stocks s
                    INNER JOIN deleted d ON s.IdProduct = d.IdProduct AND s.IdArea = d.IdArea;

                    -- Actualizar LeadTime por cada producto
                    UPDATE products
                    SET LeadTime = ISNULL((
                        SELECT AVG(DATEDIFF(day, io.OrderDate, io.ReceivedDate)) AS AvgDaysToDeliver
                        FROM inputorders io
                        WHERE io.IdProduct = products.id
                    ), 7);

                    -- Declaraciones para actualizar el Estado
                    DECLARE @IdProduct INT;
                    DECLARE @IdArea INT;
                    DECLARE @Disponibilidad DECIMAL;
                    DECLARE @LeadTime DECIMAL;
                    DECLARE @Estado NVARCHAR(15);

                    SET @IdProduct = (SELECT TOP 1 IdProduct FROM deleted);
                    SET @IdArea = (SELECT TOP 1 IdArea FROM deleted);

                    SET @Disponibilidad = (
                        (SELECT Quantity 
                        FROM stocks 
                        WHERE IdProduct = @IdProduct AND IdArea = @IdArea) * 360
                    ) / (
                        SELECT ISNULL(SUM(Quantity), 1) AS TotalQuantityConsumed
                        FROM outputorders
                        WHERE IdProduct = @IdProduct 
                        AND IdArea = @IdArea
                        AND CreatedDate >= DATEADD(day, -360, GETDATE())
                    );

                    SET @LeadTime = (SELECT LeadTime FROM products WHERE id = @IdProduct);

                    SET @Estado = 
                        CASE 
                            WHEN @Disponibilidad <= 0 THEN 'Sin stock'
                            WHEN @Disponibilidad > 0 AND @Disponibilidad <= (@LeadTime + 1) THEN 'Se agotará'
                            WHEN @Disponibilidad > (@LeadTime + 1) AND @Disponibilidad <= (@LeadTime + 10) THEN 'Pedir'
                            ELSE 'Disponible'
                        END;

                    -- Actualizar el atributo Status y EstimatedDurability
                    UPDATE stocks
                    SET
                        Status = @Estado,
                        EstimatedDurability = @Disponibilidad
                    WHERE IdProduct = @IdProduct AND IdArea = @IdArea;
                END;
            END;
        ");

        DB::unprepared("
            CREATE OR ALTER TRIGGER trg_UpdateInputOrders
            ON dbo.inputorders
            AFTER INSERT
            AS
            BEGIN
                SET NOCOUNT ON;

                DECLARE @descripcion NVARCHAR(MAX);
                DECLARE @idInserted INT;

                -- Capturar la descripción del registro insertado
                SELECT @descripcion = description, @idInserted = id
                FROM inserted;

                -- Divide la descripción en palabras clave
                DECLARE @keywords TABLE (keyword NVARCHAR(255));
                INSERT INTO @keywords (keyword)
                SELECT value
                FROM STRING_SPLIT(@descripcion, ' ')
                WHERE LEN(value) > 0;

                -- Normaliza el texto
                DECLARE @normalizedDesc NVARCHAR(MAX) = LOWER(@descripcion);

                -- Selecciona la descripción más similar y el IdProduct asociado
                WITH KeywordMatches AS (
                    SELECT 
                        io.Description AS Description,
                        io.IdProduct AS IdProduct,
                        (SELECT COUNT(*)
                        FROM @keywords k
                        WHERE LOWER(io.Description) LIKE '%' + k.keyword + '%') AS MatchCount
                    FROM inputorders io
                    WHERE io.Status = 1
                    
                    UNION ALL
                    
                    SELECT 
                        p.Name AS Description,
                        p.id AS IdProduct,
                        (SELECT COUNT(*)
                        FROM @keywords k
                        WHERE LOWER(p.Name) LIKE '%' + k.keyword + '%') AS MatchCount
                    FROM products p
                ),
                RankedMatches AS (
                    SELECT 
                        Description,
                        IdProduct,
                        MatchCount,
                        ROW_NUMBER() OVER (ORDER BY MatchCount DESC, LEN(Description) - LEN(REPLACE(Description, ' ', '')) DESC, CHARINDEX(LOWER(Description), @normalizedDesc) ASC) AS Rank
                    FROM KeywordMatches
                )
                -- Actualiza la tabla inputorders con el IdProduct encontrado
                UPDATE io
                SET io.IdProduct = rm.IdProduct
                FROM inputorders io
                INNER JOIN RankedMatches rm ON rm.Rank = 1
                WHERE io.id = @idInserted;
            END;
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inputorders');
    }
};
