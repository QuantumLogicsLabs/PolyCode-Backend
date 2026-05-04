#include <iostream>
using namespace std;
class Battery
{
public:
    int capacity;

    Battery()
    {
        cout << "Battery is being created" << endl;
    }
    ~Battery()
    {
        cout << "Battery is being destroyed" << endl;
    }
};
class Smartphone
{
private:
    Battery battery;

public:
    void charge()
    {
        cout << "Charging " << battery.capacity << endl;
    }
    void charge(int c)
    {
        battery.capacity = c;
        cout << "Charging " << battery.capacity << endl;
    }
    Smartphone()
    {
        cout << "Smartphone is being created" << endl;
    }
    ~Smartphone()
    {
        cout << "Smartphone is being destroyed" << endl;
    }
};

int main()
{
    Smartphone phone;
    phone.charge(23);
    return 0;
}