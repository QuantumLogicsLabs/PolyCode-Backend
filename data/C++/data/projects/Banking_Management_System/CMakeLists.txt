cmake_minimum_required(VERSION 3.15)
project(BankingManagementSystem VERSION 1.0.0 LANGUAGES CXX)

# Set C++ standard
set(CMAKE_CXX_STANDARD 17)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

# Set build type
if(NOT CMAKE_BUILD_TYPE)
    set(CMAKE_BUILD_TYPE Release)
endif()

# Compiler flags
set(CMAKE_CXX_FLAGS_DEBUG "-g -O0 -Wall -Wextra -Wpedantic")
set(CMAKE_CXX_FLAGS_RELEASE "-O3 -DNDEBUG -Wall -Wextra")

# Include directories
include_directories(${CMAKE_CURRENT_SOURCE_DIR}/include)

# Source files
set(SOURCES
    src/main.cpp
    src/bank.cpp
    src/account.cpp
    src/transaction.cpp
    src/user.cpp
    src/database.cpp
    src/utils.cpp
)

# Create executable
add_executable(banking_system ${SOURCES})

# Link libraries
target_link_libraries(banking_system PRIVATE)

# Installation
install(TARGETS banking_system
    RUNTIME DESTINATION bin
)

# Optional: Add tests
option(BUILD_TESTS "Build tests" OFF)
if(BUILD_TESTS)
    enable_testing()
    
    # Test sources
    set(TEST_SOURCES
        tests/test_main.cpp
        tests/test_accounts.cpp
        tests/test_transactions.cpp
        tests/test_database.cpp
    )
    
    add_executable(banking_tests ${TEST_SOURCES})
    target_link_libraries(banking_tests PRIVATE banking_system)
    
    add_test(NAME BankingSystemTests COMMAND banking_tests)
endif()

# Documentation
find_package(Doxygen QUIET)
if(DOXYGEN_FOUND)
    set(DOXYGEN_INPUT_DIR ${CMAKE_CURRENT_SOURCE_DIR}/include)
    set(DOXYGEN_OUTPUT_DIR ${CMAKE_CURRENT_BINARY_DIR}/docs)
    
    configure_file(${CMAKE_CURRENT_SOURCE_DIR}/docs/Doxyfile.in 
                   ${CMAKE_CURRENT_BINARY_DIR}/Doxyfile @ONLY)
    
    add_custom_target(docs
        ${DOXYGEN_EXECUTABLE} ${CMAKE_CURRENT_BINARY_DIR}/Doxyfile
        WORKING_DIRECTORY ${CMAKE_CURRENT_BINARY_DIR}
        COMMENT "Generating API documentation with Doxygen"
        VERBATIM
    )
endif()

# Package configuration
include(CMakePackageConfigHelpers)

configure_package_config_file(
    "${CMAKE_CURRENT_SOURCE_DIR}/BankingConfig.cmake.in"
    "${CMAKE_CURRENT_BINARY_DIR}/BankingConfig.cmake"
    INSTALL_DESTINATION lib/cmake/BankingManagementSystem
)

write_basic_package_version_file(
    "${CMAKE_CURRENT_BINARY_DIR}/BankingConfigVersion.cmake"
    VERSION ${PROJECT_VERSION}
    COMPATIBILITY SameMajorVersion
)

install(FILES
    "${CMAKE_CURRENT_BINARY_DIR}/BankingConfig.cmake"
    "${CMAKE_CURRENT_BINARY_DIR}/BankingConfigVersion.cmake"
    DESTINATION lib/cmake/BankingManagementSystem
)

# CPack configuration
include(CPack)
set(CPACK_PACKAGE_NAME "BankingManagementSystem")
set(CPACK_PACKAGE_VERSION ${PROJECT_VERSION})
set(CPACK_PACKAGE_DESCRIPTION_SUMMARY "A comprehensive banking management system")
set(CPACK_PACKAGE_VENDOR "C++ Projects")
set(CPACK_PACKAGE_CONTACT "developer@example.com")

if(WIN32)
    set(CPACK_GENERATOR "NSIS;ZIP")
elseif(APPLE)
    set(CPACK_GENERATOR "DragNDrop;TGZ")
else()
    set(CPACK_GENERATOR "DEB;RPM;TGZ")
endif()