INSERT INTO Admin (AdminID, UserName, HassadPassword, PhoneNum, UpdateVersion)
VALUES (1, 'Hassad Admin', 'admin-demo', '+966500000000', 'v1.0.0')
ON DUPLICATE KEY UPDATE UpdateVersion = VALUES(UpdateVersion);

INSERT INTO AccessLevel (AccessID, `Level`, Description, AdminID)
VALUES
(1, 'Student', 'Browse funds and subscribe', 1),
(2, 'FundManager', 'Create and manage funds', 1),
(3, 'JIC', 'Sponsor oversight', 1),
(4, 'Admin', 'Governance and updates', 1)
ON DUPLICATE KEY UPDATE
`Level` = VALUES(`Level`),
Description = VALUES(Description),
AdminID = VALUES(AdminID);

INSERT INTO Student (StudentID, StudentNameFirst, StudentNameLast, StudentPassword, StudentMajor, StudentAccountStates, StudentPhoneNumber, StudentCampus, StudentAcademicDegree, ReinvestmentAmount, AccessID)
VALUES (441434525, 'Raghad', 'Alshammari', 'Welcome!23', 'Human Resources', 'Verified', '0562313577', 'Riyadh Campus', 'Bachelor', 0, 1)
ON DUPLICATE KEY UPDATE StudentNameFirst = VALUES(StudentNameFirst);

INSERT INTO FundManager (FundManagerNumberofLicense, FundManagerID, FundManagerNameFirst, FundManagerNameLast, FundManagerPhone, FundManagerPassword, FundManagerNumberOfFund, FundManagerDateStart, FundManagerDateEnd, FundManagerAmountMaximum, FundManagerAmountMinimum, FundManagerAccountStatus, AccessID)
VALUES (414343525, 90210, 'Renad Khaled', 'Alaqahtani', '0562313577', 'Manager!23', 3, '2025-01-01', '2026-12-31', 15000, 200, 'Approved', 2)
ON DUPLICATE KEY UPDATE FundManagerNameFirst = VALUES(FundManagerNameFirst);

INSERT INTO Fund (FundID, FundTitle, InvestmentObjective, InvestmentType, FundAmountMaximum, FundAmountMinimum, HoldingPeriod, ReturnTimingPolicy, FundAccountStatus, RiskLevel, FundDateStart, FundDateEnd, ExpectedReturnPercentage, FundDescription, FundManagerNumberofLicense)
VALUES
(101, 'Sukuk Sector', 'Income Generation', 'Fixed Income (Bonds & Sukuk)', 4000, 200, 12, 'Quarterly', 'Published', 'low', '2025-01-01', '2026-01-01', 4.00, 'Provide capital appreciation by primarily investing in sovereign sukuk and public debt instruments aligned with Sharia standards.', 414343525),
(102, 'Gold Sector', 'Capital Growth', 'Commodities (Gold, Oil, etc.)', 9000, 400, 7, 'Quarterly', 'Published', 'medium', '2025-01-01', '2025-08-01', 9.00, 'Investment funds considered suitable for longer-term investors and linked to physical gold assets or futures contracts.', 414343525),
(103, 'Real Estate Sector', 'Balanced', 'Real Estate', 15000, 900, 3, 'On maturity', 'Published', 'high', '2025-01-01', '2025-04-01', 20.80, 'The Real Estate Development Fund aims to provide innovative financing programs diversified to suit all segments of society.', 414343525)
ON DUPLICATE KEY UPDATE FundTitle = VALUES(FundTitle);

INSERT INTO BankAccount (CreditCardNumber, BankAccountIban, CreditCardName, CreditCardCVV, CreditCardDayExpired, VerificationCode, PostalCode, StudentID)
VALUES ('4277889912341111', 'SA0380000000608010167519', 'RAGHAD M ALSHAMMARI', 187, '2026-01-01', 5149, '13315', 441434525)
ON DUPLICATE KEY UPDATE CreditCardName = VALUES(CreditCardName);

INSERT INTO InvestmentWallet (InvestmentWalletID, InvestmentWalletTotalAmount, InvestmentWalletReturn, InvestmentWalletCredit, StudentID)
VALUES (11, 15.00, 1.25, 200.00, 441434525)
ON DUPLICATE KEY UPDATE InvestmentWalletTotalAmount = VALUES(InvestmentWalletTotalAmount);

INSERT INTO Notification (NotificationID, NotificationDescription, VerificationCode, SentDate, FundID)
VALUES
(1, 'Verification code 5149 was sent to your phone.', 5149, CURDATE(), NULL),
(2, 'Sukuk Sector is open for subscription.', 0, CURDATE(), 101),
(3, 'Admin updated platform release to v1.0.0.', 0, CURDATE(), NULL)
ON DUPLICATE KEY UPDATE NotificationDescription = VALUES(NotificationDescription);

INSERT INTO StudentNotification (StudentID, NotificationID, TransactionID)
VALUES
(441434525, 1, NULL),
(441434525, 2, NULL)
ON DUPLICATE KEY UPDATE TransactionID = VALUES(TransactionID);

INSERT INTO FundManagerNotification (FundManagerNumberofLicense, NotificationID, TransactionID)
VALUES
(414343525, 3, NULL)
ON DUPLICATE KEY UPDATE TransactionID = VALUES(TransactionID);

INSERT INTO JicSponsor (JicSponsorID, JicUsername, JicPassword, Department, PhoneNumber, AccessID)
VALUES (31, 'jic-admin', 'jic-demo', 'JIC Investment & Innovation Center', '+966500000031', 3)
ON DUPLICATE KEY UPDATE
	JicUsername = VALUES(JicUsername),
	JicPassword = VALUES(JicPassword),
	Department = VALUES(Department),
	PhoneNumber = VALUES(PhoneNumber),
	AccessID = VALUES(AccessID);
