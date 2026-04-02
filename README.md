```mermaid
erDiagram
    USER ||--o| COINS : "has one (nullable)"
    
    USER {
        bigint id PK "Primary Key"
        string first_name "User's first name"
        string last_name "User's last name"
        string email UK "Unique Email"
        string password "Hashed string"
        timestamp created_at "Auto-managed"
        timestamp updated_at "Auto-managed"
    }

    COINS {
        bigint id PK "Primary Key"
        bigint user_id FK "Foreign Key to User"
        integer coins "The current balance"
        timestamp created_at "Auto-managed"
        timestamp updated_at "Auto-managed"
    }

    %% Logic Note: give_coin() checks USER then updates/creates COINS
    ```