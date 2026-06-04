# Evaluación de rendimiento – Prestamos App (FIM UAS)

## Casos de prueba

Comportamientos esperados para responder las preguntas:

### 1.1 Registro de usuario exitoso
- **Entrada:** Datos válidos (username, correo, contraseña)
- **Salida esperada:** Usuario creado correctamente y redirección al dashboard

### 2.1 Solicitud de préstamo exitosa
- **Entrada:** Selección de ítem, fecha, hora de inicio y duración (1-5 hrs)
- **Salida esperada:** Préstamo creado con estado "pending"; reflejado en el dashboard del usuario

### 3.1 Edición de perfil de usuario
- **Entrada:** Modificación de datos existentes (email, teléfono) y guardado
- **Salida esperada:** Datos actualizados correctamente en la base de datos

### 4.1 Integración con BD (Persistencia)
- **Entrada:** Operación CRUD sobre préstamos o reservaciones de laboratorio
- **Salida esperada:** Consistencia de datos; reflejo inmediato tras la acción

### 5.1 Validación de Login
- **Entrada:** Credenciales inexistentes o incorrectas
- **Salida esperada:** Error "Credenciales inválidas" y denegación de acceso

---

## Cuestionario de Evaluación (Aplicar para cada caso)

### Pregunta 1: Caso de prueba evaluado
Selecciona el caso:
- [ ] Registro de usuario exitoso
- [ ] Solicitud de préstamo exitosa
- [ ] Edición de perfil de usuario
- [ ] Integración con Base de Datos (Persistencia)
- [ ] Validación de Login

### Pregunta 2: Acierto de la salida
¿La salida coincide con lo esperado?
- Deficiente 1 – 2 – 3 – 4 – 5 Excelente

### Pregunta 3: Tiempo de respuesta
¿El sistema responde en un tiempo aceptable?
- Deficiente 1 – 2 – 3 – 4 – 5 Excelente

### Pregunta 4: Claridad de la interfaz durante la prueba (UX)
- Deficiente 1 – 2 – 3 – 4 – 5 Excelente

### Pregunta 5: Manejo adecuado de errores
- Deficiente 1 – 2 – 3 – 4 – 5 Excelente

### Pregunta 6: Consistencia del sistema
¿Se comporta igual en ejecuciones repetidas?
- Deficiente 1 – 2 – 3 – 4 – 5 Excelente

### Pregunta 7: Estabilidad del sistema durante la prueba
- Deficiente 1 – 2 – 3 – 4 – 5 Excelente

### Pregunta 8: Accesibilidad
¿La navegación por teclado es fluida en este módulo?
- Deficiente 1 – 2 – 3 – 4 – 5 Excelente

### Pregunta 9: Resultado final de la prueba
¿Se cumplió con el criterio de aceptación?
- [ ] Aprobada
- [ ] Reprobada

### Pregunta 10: Observaciones generales
(Texto libre)
