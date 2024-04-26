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
        Schema::create('outputorders', function (Blueprint $table) {
            $table->id();
            $table->datetime('CreatedDate')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('EstimatedDurability',10,2)->nullable();
            $table->decimal('Quantity',10,2);
            $table->string('UnitMeasurement')->nullable();
            //$table->integer('IdEmployee');

            // Crea atributo de llave foránea
            $table->foreignId('IdProduct')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            // Crea atributo de llave foránea
            $table->foreignId('IdEmployee')->constrained('employees')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('IdArea')->constrained('areas')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });

        // Trigger para insertar al stock las cantidades ingresadas
        DB::unprepared("
                        
            CREATE OR ALTER TRIGGER trg_UpdateStockQuantityOut
            ON dbo.outputorders
            AFTER INSERT
            AS
            BEGIN
                SET NOCOUNT ON;

                -- Actualizar la tabla stocks con la nueva cantidad
                UPDATE s
                SET s.Quantity = s.Quantity - i.Quantity
                FROM dbo.stocks s
                INNER JOIN inserted i ON s.IdProduct = i.IdProduct AND s.IdArea = i.IdArea;
            
                --update estado y disponibilidad

                
-- para Actualizar el Estado
DECLARE @IdProduct INT;
DECLARE @IdArea INT;
DECLARE @Disponibilidad DECIMAL;
DECLARE @LeadTime DECIMAL;
DECLARE @Estado NVARCHAR(15);

SET @IdProduct = (
    SELECT top 1 IdProduct 
    FROM inserted 
);
SET @IdArea = (
    SELECT top 1 IdArea 
    FROM inserted
);


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

    SET @LeadTime = (
        SELECT LeadTime 
        FROM products 
        WHERE id = @IdProduct
    );

    SET @Estado = 
        CASE 
            WHEN @Disponibilidad <= 0 THEN 'Sin stock'
            WHEN @Disponibilidad > 0 AND @Disponibilidad <= (@LeadTime + 1) THEN 'Se agotará'
            WHEN @Disponibilidad > (@LeadTime + 1) AND @Disponibilidad <= (@LeadTime + 10) THEN 'Pedir'
            ELSE 'Disponible'
        END;

    -- Actualizar el atributo Status
    UPDATE stocks
    SET
		Status = @Estado,
		EstimatedDurability = @Disponibilidad
    WHERE IdProduct = @IdProduct AND IdArea = @IdArea;

                --fin update estado y disponibilidad
            
                END;
        ");
        //trigger para eliminar del stock cantidades ingresadas al stock
        DB::unprepared("
            CREATE OR ALTER TRIGGER trg_UpdateStockQuantityOnDeleteOut
            ON dbo.outputorders
            AFTER DELETE
            AS
            BEGIN
                SET NOCOUNT ON;
            
                -- Actualizar la tabla stocks restando la cantidad eliminada
                UPDATE s
                SET s.Quantity = s.Quantity + d.Quantity
                FROM dbo.stocks s
                INNER JOIN deleted d ON s.IdProduct = d.IdProduct AND s.IdArea = d.IdArea;
                     
-- para Actualizar el Estado
DECLARE @IdProduct INT;
DECLARE @IdArea INT;
DECLARE @Disponibilidad DECIMAL;
DECLARE @LeadTime DECIMAL;
DECLARE @Estado NVARCHAR(15);

SET @IdProduct = (
    SELECT top 1 IdProduct 
    FROM deleted 
);
SET @IdArea = (
    SELECT top 1 IdArea 
    FROM deleted
);


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

    SET @LeadTime = (
        SELECT LeadTime 
        FROM products 
        WHERE id = @IdProduct
    );

    SET @Estado = 
        CASE 
            WHEN @Disponibilidad <= 0 THEN 'Sin stock'
            WHEN @Disponibilidad > 0 AND @Disponibilidad <= (@LeadTime + 1) THEN 'Se agotará'
            WHEN @Disponibilidad > (@LeadTime + 1) AND @Disponibilidad <= (@LeadTime + 10) THEN 'Pedir'
            ELSE 'Disponible'
        END;

    -- Actualizar el atributo Status
    UPDATE stocks
    SET
		Status = @Estado,
		EstimatedDurability = @Disponibilidad
    WHERE IdProduct = @IdProduct AND IdArea = @IdArea;

                --fin update estado y disponibilidad
            
                END;
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outputorders');
    }
};
