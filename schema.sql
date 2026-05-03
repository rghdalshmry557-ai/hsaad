CREATE TABLE IF NOT EXISTS Admin (
    AdminID INT PRIMARY KEY,
    UserName VARCHAR(100),
    HassadPassword VARCHAR(255),
    PhoneNum VARCHAR(20),
    UpdateVersion VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS AccessLevel (
    AccessID INT PRIMARY KEY,
    `Level` VARCHAR(50),
    Description VARCHAR(255),
    AdminID INT,
    FOREIGN KEY (AdminID) REFERENCES Admin(AdminID)
);

CREATE TABLE IF NOT EXISTS Student (
    StudentID INT PRIMARY KEY,
    StudentNameFirst VARCHAR(100),
    StudentNameLast VARCHAR(100),
    StudentPassword VARCHAR(255),
    StudentMajor VARCHAR(100),
    StudentAccountStates VARCHAR(50),
    StudentPhoneNumber VARCHAR(20),
    StudentCampus VARCHAR(100),
    StudentAcademicDegree VARCHAR(100),
    ReinvestmentAmount DECIMAL(12,2) DEFAULT 0,
    AccessID INT,
    FOREIGN KEY (AccessID) REFERENCES AccessLevel(AccessID)
);

CREATE TABLE IF NOT EXISTS FundManager (
    FundManagerNumberofLicense INT PRIMARY KEY,
    FundManagerID INT,
    FundManagerNameFirst VARCHAR(100),
    FundManagerNameLast VARCHAR(100),
    FundManagerPhone VARCHAR(20),
    FundManagerPassword VARCHAR(255),
    FundManagerNumberOfFund INT DEFAULT 0,
    FundManagerDateStart DATE,
    FundManagerDateEnd DATE,
    FundManagerAmountMaximum DECIMAL(12,2),
    FundManagerAmountMinimum DECIMAL(12,2),
    FundManagerAccountStatus VARCHAR(50),
    AccessID INT,
    FOREIGN KEY (AccessID) REFERENCES AccessLevel(AccessID)
);

CREATE TABLE IF NOT EXISTS Fund (
    FundID INT PRIMARY KEY,
    FundTitle VARCHAR(100),
    InvestmentObjective VARCHAR(100),
    InvestmentType VARCHAR(100),
    FundAmountMaximum DECIMAL(12,2),
    FundAmountMinimum DECIMAL(12,2),
    HoldingPeriod INT,
    ReturnTimingPolicy VARCHAR(100),
    FundAccountStatus VARCHAR(50),
    RiskLevel VARCHAR(50),
    FundDateStart DATE,
    FundDateEnd DATE,
    ExpectedReturnPercentage DECIMAL(6,2),
    FundDescription TEXT,
    FundManagerNumberofLicense INT,
    FOREIGN KEY (FundManagerNumberofLicense) REFERENCES FundManager(FundManagerNumberofLicense)
);

CREATE TABLE IF NOT EXISTS BankAccount (
    CreditCardNumber VARCHAR(50) PRIMARY KEY,
    BankAccountIban VARCHAR(50),
    CreditCardName VARCHAR(100),
    CreditCardCVV INT,
    CreditCardDayExpired DATE,
    VerificationCode INT,
    PostalCode VARCHAR(20),
    StudentID INT,
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID)
);

CREATE TABLE IF NOT EXISTS InvestmentWallet (
    InvestmentWalletID INT PRIMARY KEY,
    InvestmentWalletTotalAmount DECIMAL(12,2) DEFAULT 0,
    InvestmentWalletReturn DECIMAL(12,2) DEFAULT 0,
    InvestmentWalletCredit DECIMAL(12,2) DEFAULT 0,
    StudentID INT,
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID)
);

CREATE TABLE IF NOT EXISTS Contract (
    ContractID INT PRIMARY KEY,
    RolesDescription VARCHAR(255),
    FundID INT,
    StudentID INT,
    HassadID INT,
    FOREIGN KEY (FundID) REFERENCES Fund(FundID),
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID)
);

CREATE TABLE IF NOT EXISTS `Transaction` (
    TransactionID INT PRIMARY KEY,
    TransactionType VARCHAR(50),
    Direction VARCHAR(50),
    FundCapital DECIMAL(12,2) DEFAULT 0,
    TotalCapital DECIMAL(12,2) DEFAULT 0,
    FundReturns DECIMAL(12,2) DEFAULT 0,
    TotalReturns DECIMAL(12,2) DEFAULT 0,
    FundWithdrawnProfit DECIMAL(12,2) DEFAULT 0,
    TotalWithdrawnProfit DECIMAL(12,2) DEFAULT 0,
    FundReinvestedReturns DECIMAL(12,2) DEFAULT 0,
    TotalReinvestedReturns DECIMAL(12,2) DEFAULT 0,
    FundFullWithdrawalAmount DECIMAL(12,2) DEFAULT 0,
    TotalFullWithdrawalAmount DECIMAL(12,2) DEFAULT 0,
    StudentID INT,
    CreditCardNumber VARCHAR(50),
    FundID INT,
    FundManagerNumberofLicense INT,
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
    FOREIGN KEY (CreditCardNumber) REFERENCES BankAccount(CreditCardNumber),
    FOREIGN KEY (FundID) REFERENCES Fund(FundID),
    FOREIGN KEY (FundManagerNumberofLicense) REFERENCES FundManager(FundManagerNumberofLicense)
);

CREATE TABLE IF NOT EXISTS Notification (
    NotificationID INT PRIMARY KEY,
    NotificationDescription VARCHAR(255),
    VerificationCode INT,
    SentDate DATE,
    FundID INT,
    FOREIGN KEY (FundID) REFERENCES Fund(FundID)
);

CREATE TABLE IF NOT EXISTS StudentNotification (
    StudentID INT,
    NotificationID INT,
    TransactionID INT NULL,
    PRIMARY KEY (StudentID, NotificationID),
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
    FOREIGN KEY (NotificationID) REFERENCES Notification(NotificationID),
    FOREIGN KEY (TransactionID) REFERENCES `Transaction`(TransactionID)
);

CREATE TABLE IF NOT EXISTS FundManagerNotification (
    FundManagerNumberofLicense INT,
    NotificationID INT,
    TransactionID INT NULL,
    PRIMARY KEY (FundManagerNumberofLicense, NotificationID),
    FOREIGN KEY (FundManagerNumberofLicense) REFERENCES FundManager(FundManagerNumberofLicense),
    FOREIGN KEY (NotificationID) REFERENCES Notification(NotificationID),
    FOREIGN KEY (TransactionID) REFERENCES `Transaction`(TransactionID)
);
CREATE TABLE IF NOT EXISTS JicSponsor (
    JicSponsorID INT PRIMARY KEY,
    JicUsername VARCHAR(100),
    JicPassword VARCHAR(255),
    Department VARCHAR(100),
    PhoneNumber VARCHAR(20),
    AccessID INT,
    FOREIGN KEY (AccessID) REFERENCES AccessLevel(AccessID)
);

-- جدول حظر المستخدمين
CREATE TABLE IF NOT EXISTS UserBan (
    BanID INT PRIMARY KEY AUTO_INCREMENT,
    UserType ENUM('Student', 'FundManager', 'Admin') NOT NULL,
    UserID INT NOT NULL,
    BanReason VARCHAR(500),
    BannedBy INT,
    BannedByType ENUM('Admin', 'FundManager', 'JIC', 'Self') NOT NULL,
    BanDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    UnbanDate DATETIME NULL,
    IsActive BOOLEAN DEFAULT TRUE,
    INDEX idx_user (UserType, UserID),
    INDEX idx_active (IsActive)
);

-- جدول إعدادات النظام
CREATE TABLE IF NOT EXISTS SystemSettings (
    SettingID INT PRIMARY KEY AUTO_INCREMENT,
    SettingKey VARCHAR(100) UNIQUE NOT NULL,
    SettingValue TEXT,
    SettingDescription VARCHAR(255),
    UpdatedBy INT,
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- جدول سجل الرسائل النصية
CREATE TABLE IF NOT EXISTS SmsLog (
    SmsID INT PRIMARY KEY AUTO_INCREMENT,
    RecipientPhone VARCHAR(20) NOT NULL,
    RecipientType ENUM('Student', 'FundManager', 'Admin', 'All') NOT NULL,
    RecipientID INT NULL,
    MessageContent TEXT NOT NULL,
    MessageType ENUM('Notification', 'Verification', 'Marketing', 'Alert') NOT NULL,
    SentBy INT,
    SentAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('Pending', 'Sent', 'Failed', 'Delivered') DEFAULT 'Pending',
    INDEX idx_recipient (RecipientType, RecipientID),
    INDEX idx_status (Status)
);

-- جدول توزيع العوائد
CREATE TABLE IF NOT EXISTS ReturnDistribution (
    DistributionID INT PRIMARY KEY AUTO_INCREMENT,
    FundID INT NOT NULL,
    DistributionDate DATE NOT NULL,
    TotalAmount DECIMAL(12,2) NOT NULL,
    DistributionType ENUM('Quarterly', 'Monthly', 'OnMaturity', 'Special') NOT NULL,
    Status ENUM('Pending', 'Processing', 'Completed', 'Failed') DEFAULT 'Pending',
    CreatedBy INT NOT NULL,
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    CompletedAt DATETIME NULL,
    FOREIGN KEY (FundID) REFERENCES Fund(FundID),
    INDEX idx_fund_date (FundID, DistributionDate)
);

-- جدول تفاصيل توزيع العوائد
CREATE TABLE IF NOT EXISTS ReturnDistributionDetail (
    DetailID INT PRIMARY KEY AUTO_INCREMENT,
    DistributionID INT NOT NULL,
    StudentID INT NOT NULL,
    ContractID INT NOT NULL,
    InvestedAmount DECIMAL(12,2) NOT NULL,
    ReturnAmount DECIMAL(12,2) NOT NULL,
    ReturnPercentage DECIMAL(6,2) NOT NULL,
    Status ENUM('Pending', 'Distributed', 'Reinvested', 'Withdrawn') DEFAULT 'Pending',
    FOREIGN KEY (DistributionID) REFERENCES ReturnDistribution(DistributionID),
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
    FOREIGN KEY (ContractID) REFERENCES Contract(ContractID),
    INDEX idx_distribution (DistributionID),
    INDEX idx_student (StudentID)
);

-- جدول طلبات السحب
CREATE TABLE IF NOT EXISTS WithdrawalRequest (
    WithdrawalID INT PRIMARY KEY AUTO_INCREMENT,
    StudentID INT NOT NULL,
    FundID INT NULL,
    Amount DECIMAL(12,2) NOT NULL,
    WithdrawalType ENUM('Profit', 'Full', 'Partial') NOT NULL,
    BankAccountIban VARCHAR(50),
    Status ENUM('Pending', 'Approved', 'Processing', 'Completed', 'Rejected') DEFAULT 'Pending',
    RequestDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    ProcessedDate DATETIME NULL,
    ProcessedBy INT NULL,
    RejectionReason VARCHAR(500) NULL,
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
    FOREIGN KEY (FundID) REFERENCES Fund(FundID),
    INDEX idx_student (StudentID),
    INDEX idx_status (Status)
);

-- جدول الموافقات على المشاركة
CREATE TABLE IF NOT EXISTS ParticipationApproval (
    ApprovalID INT PRIMARY KEY AUTO_INCREMENT,
    StudentID INT NOT NULL,
    FundID INT NOT NULL,
    ApprovalStatus ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    ApprovedBy INT NULL,
    ApprovalDate DATETIME NULL,
    RequestDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    Notes VARCHAR(500) NULL,
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
    FOREIGN KEY (FundID) REFERENCES Fund(FundID),
    UNIQUE KEY unique_student_fund (StudentID, FundID)
);

-- جدول سجل الخصومات
CREATE TABLE IF NOT EXISTS StudentDeduction (
    DeductionID INT PRIMARY KEY AUTO_INCREMENT,
    StudentID INT NOT NULL,
    FundID INT NULL,
    DeductionType ENUM('Subscription', 'Fee', 'Penalty', 'Other') NOT NULL,
    Amount DECIMAL(12,2) NOT NULL,
    Description VARCHAR(255),
    DeductionDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
    FOREIGN KEY (FundID) REFERENCES Fund(FundID),
    INDEX idx_student (StudentID),
    INDEX idx_date (DeductionDate)
);

-- جدول التحقق الثنائي للصناديق
CREATE TABLE IF NOT EXISTS FundVerification (
    VerificationID INT PRIMARY KEY AUTO_INCREMENT,
    FundID INT NOT NULL,
    FundManagerLicense INT NOT NULL,
    OperationType ENUM('Publish', 'Hide', 'Update', 'DistributeReturns', 'Delete') NOT NULL,
    VerificationCode INT NOT NULL,
    CodeSentAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    CodeExpiresAt DATETIME NOT NULL,
    IsVerified BOOLEAN DEFAULT FALSE,
    VerifiedAt DATETIME NULL,
    FOREIGN KEY (FundID) REFERENCES Fund(FundID),
    FOREIGN KEY (FundManagerLicense) REFERENCES FundManager(FundManagerNumberofLicense),
    INDEX idx_fund (FundID),
    INDEX idx_manager (FundManagerLicense)
);

-- جدول سجل التدقيق
CREATE TABLE IF NOT EXISTS AuditLog (
    LogID INT PRIMARY KEY AUTO_INCREMENT,
    UserType ENUM('Student', 'FundManager', 'Admin', 'JIC', 'System') NOT NULL,
    UserID INT NULL,
    ActionType VARCHAR(100) NOT NULL,
    ActionDescription TEXT,
    EntityType VARCHAR(50),
    EntityID INT NULL,
    OldValue TEXT NULL,
    NewValue TEXT NULL,
    IpAddress VARCHAR(45),
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (UserType, UserID),
    INDEX idx_action (ActionType),
    INDEX idx_entity (EntityType, EntityID),
    INDEX idx_date (CreatedAt)
);

-- جدول قبول الشروط والأحكام
CREATE TABLE IF NOT EXISTS TermsAcceptance (
    AcceptanceID INT PRIMARY KEY AUTO_INCREMENT,
    UserType ENUM('Student', 'FundManager') NOT NULL,
    UserID INT NOT NULL,
    TermsVersion VARCHAR(20) NOT NULL,
    AcceptedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    IpAddress VARCHAR(45),
    UNIQUE KEY unique_user_terms (UserType, UserID, TermsVersion)
);