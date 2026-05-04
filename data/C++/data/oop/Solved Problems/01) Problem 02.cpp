#include <iostream>
using namespace std;
class Driver
{
public:
    void drive()
    {
        cout << "Driving..." << endl;
    }
    Driver()
    {
        cout << "Driver is being created" << endl;
    }
    ~Driver()
    {
        cout << "Driver is being destroyed" << endl;
    }
};
class Taxi
{
private:
    Driver *driver;

public:
    void start()
    {
        driver->drive();
    }
    Taxi()
    {
        cout << "Taxi is being created" << endl;
    }
    ~Taxi()
    {
        cout << "Taxi is being destroyed" << endl;
    }
};
int main()
{
    Taxi taxi;
    taxi.start();
    return 0;
}